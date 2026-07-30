package com.example.ui

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.example.ai.GeminiService
import com.example.data.audio.AudioEngine
import com.example.data.local.AppDatabase
import com.example.data.model.CombatState
import com.example.data.model.LocationEntity
import com.example.data.model.NpcEntity
import com.example.data.model.SfxPadEntity
import com.example.data.model.VoiceChronicleEntity
import com.example.data.model.WorldEntity
import com.example.repository.BardoRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch

import android.os.Build

data class BardoUiState(
    val selectedWorldId: Long = 1L,
    val selectedLocation: LocationEntity? = null,
    val aiDescription: String = "",
    val isAiLoading: Boolean = false,
    val isRecordingVoice: Boolean = false,
    val voiceTranscriptInput: String = "",
    val diceResultText: String = "",
    val sessionCode: String = "SALA-BARDO-88",
    val livePlayerLogs: List<String> = listOf(
        "🔮 A Taverna conectada em obardo.com.br/sala/SALA-BARDO-88",
        "🎵 Trilha iniciada: Exploração Arcana",
        "⚔️ Mestre acionou o Chaveador de Áudio"
    )
)

class BardoViewModel(application: Application) : AndroidViewModel(application) {

    private val db = AppDatabase.getInstance(application)
    private val repository = BardoRepository(db.bardoDao())
    val audioEngine = AudioEngine(
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            application.createAttributionContext("audio")
        } else {
            application
        }
    )
    private val geminiService = GeminiService()

    val worlds: StateFlow<List<WorldEntity>> = repository.allWorlds.stateIn(
        scope = viewModelScope,
        started = SharingStarted.WhileSubscribed(5000),
        initialValue = emptyList()
    )

    val allChronicles: StateFlow<List<VoiceChronicleEntity>> = repository.allChronicles.stateIn(
        scope = viewModelScope,
        started = SharingStarted.WhileSubscribed(5000),
        initialValue = emptyList()
    )

    val sfxPads: StateFlow<List<SfxPadEntity>> = repository.allSfxPads.stateIn(
        scope = viewModelScope,
        started = SharingStarted.WhileSubscribed(5000),
        initialValue = emptyList()
    )

    private val _uiState = MutableStateFlow(BardoUiState())
    val uiState: StateFlow<BardoUiState> = _uiState.asStateFlow()

    private val _locations = MutableStateFlow<List<LocationEntity>>(emptyList())
    val locations: StateFlow<List<LocationEntity>> = _locations.asStateFlow()

    private val _npcs = MutableStateFlow<List<NpcEntity>>(emptyList())
    val npcs: StateFlow<List<NpcEntity>> = _npcs.asStateFlow()

    private val _locationChronicles = MutableStateFlow<List<VoiceChronicleEntity>>(emptyList())
    val locationChronicles: StateFlow<List<VoiceChronicleEntity>> = _locationChronicles.asStateFlow()

    init {
        viewModelScope.launch {
            repository.seedDefaultDataIfEmpty()
        }

        // Observe worlds & locations
        viewModelScope.launch {
            worlds.collectLatest { worldList ->
                if (worldList.isNotEmpty() && _uiState.value.selectedWorldId == 1L) {
                    selectWorld(worldList.first().id)
                }
            }
        }

        viewModelScope.launch {
            repository.currentSceneLocation.collectLatest { sceneLoc ->
                if (sceneLoc != null) {
                    _uiState.value = _uiState.value.copy(selectedLocation = sceneLoc)
                    loadLocationDetails(sceneLoc)
                }
            }
        }
    }

    fun selectWorld(worldId: Long) {
        _uiState.value = _uiState.value.copy(selectedWorldId = worldId)
        viewModelScope.launch {
            repository.getLocationsForWorld(worldId).collectLatest { locList ->
                _locations.value = locList
                if (locList.isNotEmpty() && _uiState.value.selectedLocation == null) {
                    selectLocation(locList.first())
                }
            }
        }
    }

    fun selectLocation(location: LocationEntity) {
        _uiState.value = _uiState.value.copy(
            selectedLocation = location,
            aiDescription = location.description
        )
        viewModelScope.launch {
            repository.selectSceneLocation(location.id)
        }

        // Adapt sound immediately with smooth crossfade!
        audioEngine.setAmbientSound(location.ambientSoundKey)
        audioEngine.setMusicTrack(location.musicTrackKey)

        addPlayerLog("🏰 Mestre mudou a cena para: ${location.name}")
        loadLocationDetails(location)
    }

    private fun loadLocationDetails(location: LocationEntity) {
        viewModelScope.launch {
            repository.getNpcsForLocation(location.id).collectLatest { npcList ->
                _npcs.value = npcList
            }
        }
        viewModelScope.launch {
            repository.getChroniclesForLocation(location.id).collectLatest { chronList ->
                _locationChronicles.value = chronList
            }
        }
    }

    fun toggleCombatState() {
        val nextState = when (audioEngine.combatState.value) {
            CombatState.EXPLORATION -> CombatState.TENSION
            CombatState.TENSION -> CombatState.COMBAT
            CombatState.COMBAT -> CombatState.EXPLORATION
        }
        audioEngine.setCombatState(nextState)
        addPlayerLog("⚔️ Chaveador de Combate: ${nextState.displayName}")
    }

    fun setCombatStateDirect(state: CombatState) {
        audioEngine.setCombatState(state)
        addPlayerLog("⚔️ Chaveador de Combate alterado para: ${state.displayName}")
    }

    fun triggerSfx(sfxType: String, name: String) {
        audioEngine.playSfx(sfxType)
        addPlayerLog("🔊 Efeito Sonoro (SFX): $name disparo no PWA!")
    }

    fun describeSceneWithAi() {
        val currentLoc = _uiState.value.selectedLocation ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isAiLoading = true)
            val desc = geminiService.describeScene(currentLoc.name, currentLoc.parentLocation)
            _uiState.value = _uiState.value.copy(
                aiDescription = desc,
                isAiLoading = false
            )
            addPlayerLog("✨ O Bardo gerou nova descrição imersiva da cena!")
        }
    }

    fun generateSecretWithAi() {
        val currentLoc = _uiState.value.selectedLocation ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isAiLoading = true)
            val secret = geminiService.generateSecret(currentLoc.name)
            val updated = currentLoc.copy(secrets = secret)
            repository.updateLocation(updated)
            _uiState.value = _uiState.value.copy(
                selectedLocation = updated,
                isAiLoading = false
            )
            addPlayerLog("🔒 Novo segredo revelado pela IA do Bardo!")
        }
    }

    fun saveVoiceChronicle(rawNote: String) {
        if (rawNote.isBlank()) return
        val currentLoc = _uiState.value.selectedLocation ?: return
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isAiLoading = true)
            val formatted = geminiService.formatVoiceChronicle(rawNote, currentLoc.name)
            repository.addVoiceChronicle(
                VoiceChronicleEntity(
                    locationId = currentLoc.id,
                    rawText = rawNote,
                    formattedLore = formatted
                )
            )
            _uiState.value = _uiState.value.copy(
                isAiLoading = false,
                voiceTranscriptInput = ""
            )
            addPlayerLog("📜 Crônica gravada por voz e adicionada ao diário!")
        }
    }

    fun rollDice(sides: Int, modifier: Int = 0) {
        val roll = (1..sides).random()
        val total = roll + modifier
        val modText = if (modifier != 0) " (${if (modifier > 0) "+$modifier" else "$modifier"})" else ""
        val isCrit = roll == sides
        val isFail = roll == 1
        val result = "d$sides: $roll$modText = $total ${if (isCrit) "🔥 CRÍTICO!" else if (isFail) "💀 FALHA CRÍTICA!" else ""}"

        _uiState.value = _uiState.value.copy(diceResultText = result)
        audioEngine.playSfx("DICE")
        addPlayerLog("🎲 Rolagem de Dado: $result")
    }

    fun addNpc(name: String, role: String, notes: String, quote: String) {
        val loc = _uiState.value.selectedLocation ?: return
        viewModelScope.launch {
            repository.addNpc(
                NpcEntity(
                    locationId = loc.id,
                    name = name,
                    role = role,
                    notes = notes,
                    quote = quote
                )
            )
        }
    }

    fun deleteNpc(npcId: Long) {
        viewModelScope.launch {
            repository.deleteNpc(npcId)
        }
    }

    fun addLocation(name: String, parent: String, description: String, ambientKey: String, musicKey: String) {
        val worldId = _uiState.value.selectedWorldId
        viewModelScope.launch {
            val newId = repository.addLocation(
                LocationEntity(
                    worldId = worldId,
                    name = name,
                    parentLocation = parent,
                    description = description,
                    ambientSoundKey = ambientKey,
                    musicTrackKey = musicKey
                )
            )
            addPlayerLog("🗺️ Novo local adicionado: $name")
        }
    }

    fun addWorld(name: String, system: String, description: String) {
        viewModelScope.launch {
            val newId = repository.addWorld(name, system, description)
            selectWorld(newId)
            addPlayerLog("🌐 Novo mundo/campanha criada: $name ($system)")
        }
    }

    private fun addPlayerLog(message: String) {
        val current = _uiState.value.livePlayerLogs.toMutableList()
        current.add(0, message)
        if (current.size > 20) current.removeAt(current.size - 1)
        _uiState.value = _uiState.value.copy(livePlayerLogs = current)
    }

    override fun onCleared() {
        super.onCleared()
        audioEngine.release()
    }
}
