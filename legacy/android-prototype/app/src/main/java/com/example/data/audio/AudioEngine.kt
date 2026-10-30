package com.example.data.audio

import android.media.AudioAttributes
import android.media.AudioFormat
import android.media.AudioTrack
import android.util.Log
import com.example.data.model.CombatState
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.Job
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.isActive
import android.content.Context
import kotlinx.coroutines.launch
import kotlin.math.sin

class AudioEngine(private val context: Context? = null) {

    private val sampleRate = 22050
    private val scope = CoroutineScope(Dispatchers.Default)

    private var ambientTrack: AudioTrack? = null
    private var musicTrack: AudioTrack? = null

    private var ambientJob: Job? = null
    private var musicJob: Job? = null

    private val _ambientVolume = MutableStateFlow(0.7f)
    val ambientVolume: StateFlow<Float> = _ambientVolume.asStateFlow()

    private val _musicVolume = MutableStateFlow(0.8f)
    val musicVolume: StateFlow<Float> = _musicVolume.asStateFlow()

    private val _masterVolume = MutableStateFlow(0.9f)
    val masterVolume: StateFlow<Float> = _masterVolume.asStateFlow()

    private val _currentAmbient = MutableStateFlow("RAIN")
    val currentAmbient: StateFlow<String> = _currentAmbient.asStateFlow()

    private val _currentMusic = MutableStateFlow("EXPLORATION")
    val currentMusic: StateFlow<String> = _currentMusic.asStateFlow()

    private val _currentPreset = MutableStateFlow("CUSTOM")
    val currentPreset: StateFlow<String> = _currentPreset.asStateFlow()

    private val _combatState = MutableStateFlow(CombatState.EXPLORATION)
    val combatState: StateFlow<CombatState> = _combatState.asStateFlow()

    private val _isPlaying = MutableStateFlow(true)
    val isPlaying: StateFlow<Boolean> = _isPlaying.asStateFlow()

    init {
        startAmbientLoop(_currentAmbient.value)
        startMusicLoop(_currentMusic.value, _combatState.value)
    }

    fun applyPreset(presetName: String) {
        _currentPreset.value = presetName
        when (presetName) {
            "TAVERN" -> {
                setAmbientSound("TAVERN")
                setMusicTrack("EXPLORATION")
                setCombatState(CombatState.EXPLORATION)
            }
            "CASTLE" -> {
                setAmbientSound("RAIN")
                setMusicTrack("MYSTERY")
                setCombatState(CombatState.TENSION)
            }
            "FOREST" -> {
                setAmbientSound("WIND")
                setMusicTrack("INVESTIGATION")
                setCombatState(CombatState.EXPLORATION)
            }
            "DUNGEON" -> {
                setAmbientSound("FIRE")
                setMusicTrack("MYSTERY")
                setCombatState(CombatState.TENSION)
            }
            "BATTLE" -> {
                setAmbientSound("DUNGEON")
                setMusicTrack("EXPLORATION")
                setCombatState(CombatState.COMBAT)
            }
            "MYSTIC" -> {
                setAmbientSound("MYSTIC")
                setMusicTrack("INVESTIGATION")
                setCombatState(CombatState.EXPLORATION)
            }
        }
    }

    fun setAmbientVolume(vol: Float) {
        _ambientVolume.value = vol.coerceIn(0f, 1f)
    }

    fun setMusicVolume(vol: Float) {
        _musicVolume.value = vol.coerceIn(0f, 1f)
    }

    fun setMasterVolume(vol: Float) {
        _masterVolume.value = vol.coerceIn(0f, 1f)
    }

    fun setAmbientSound(ambientKey: String) {
        if (_currentAmbient.value == ambientKey && ambientJob?.isActive == true) return
        _currentAmbient.value = ambientKey
        startAmbientLoop(ambientKey)
    }

    fun setMusicTrack(musicKey: String) {
        if (_currentMusic.value == musicKey && musicJob?.isActive == true && _combatState.value == CombatState.EXPLORATION) return
        _currentMusic.value = musicKey
        startMusicLoop(musicKey, _combatState.value)
    }

    fun setCombatState(state: CombatState) {
        _combatState.value = state
        startMusicLoop(_currentMusic.value, state)
    }

    fun togglePlayPause() {
        _isPlaying.value = !_isPlaying.value
        if (!_isPlaying.value) {
            ambientJob?.cancel()
            musicJob?.cancel()
        } else {
            startAmbientLoop(_currentAmbient.value)
            startMusicLoop(_currentMusic.value, _combatState.value)
        }
    }

    private fun startAmbientLoop(key: String) {
        ambientJob?.cancel()
        ambientJob = scope.launch {
            try {
                val minBufSize = AudioTrack.getMinBufferSize(
                    sampleRate,
                    AudioFormat.CHANNEL_OUT_MONO,
                    AudioFormat.ENCODING_PCM_16BIT
                )
                val track = AudioTrack.Builder()
                    .setAudioAttributes(
                        AudioAttributes.Builder()
                            .setUsage(AudioAttributes.USAGE_MEDIA)
                            .setContentType(AudioAttributes.CONTENT_TYPE_MUSIC)
                            .build()
                    )
                    .setAudioFormat(
                        AudioFormat.Builder()
                            .setEncoding(AudioFormat.ENCODING_PCM_16BIT)
                            .setSampleRate(sampleRate)
                            .setChannelMask(AudioFormat.CHANNEL_OUT_MONO)
                            .build()
                    )
                    .setBufferSizeInBytes(minBufSize * 2)
                    .setTransferMode(AudioTrack.MODE_STREAM)
                    .build()

                track.play()
                ambientTrack = track

                val buffer = ShortArray(1024)
                var phase = 0.0

                while (isActive && _isPlaying.value) {
                    val vol = _ambientVolume.value * _masterVolume.value
                    for (i in buffer.indices) {
                        val sample = when (key) {
                            "RAIN" -> {
                                // Filtered pink noise + rain drops
                                val noise = (Math.random() * 2 - 1) * 0.25
                                val drop = if (Math.random() < 0.002) sin(phase * 15) * 0.6 else 0.0
                                (noise + drop)
                            }
                            "FIRE" -> {
                                // Crackle fire brown noise
                                val brown = (Math.random() * 2 - 1) * 0.3
                                val pop = if (Math.random() < 0.005) (Math.random() * 0.8) else 0.0
                                (brown + pop)
                            }
                            "DUNGEON" -> {
                                // Sub drone (55Hz / 110Hz)
                                val drone = sin(2.0 * Math.PI * 55.0 * phase / sampleRate) * 0.4 +
                                            sin(2.0 * Math.PI * 110.0 * phase / sampleRate) * 0.2
                                val wind = (Math.random() * 2 - 1) * 0.1
                                drone + wind
                            }
                            "TAVERN" -> {
                                // Cozy chatter ambience (muffled noise + light clink)
                                val hum = sin(2.0 * Math.PI * 180.0 * phase / sampleRate) * 0.2
                                val clink = if (Math.random() < 0.001) sin(2.0 * Math.PI * 1200.0 * phase / sampleRate) * 0.5 else 0.0
                                val noise = (Math.random() * 2 - 1) * 0.15
                                hum + clink + noise
                            }
                            "WIND" -> {
                                // Sweeping wind oscillator
                                val mod = (sin(2.0 * Math.PI * 0.2 * phase / sampleRate) + 1.0) * 0.5
                                val noise = (Math.random() * 2 - 1) * (0.1 + mod * 0.3)
                                noise
                            }
                            else -> {
                                // MYSTIC organ chord
                                val c = sin(2.0 * Math.PI * 220.0 * phase / sampleRate) * 0.2 +
                                        sin(2.0 * Math.PI * 277.18 * phase / sampleRate) * 0.2 +
                                        sin(2.0 * Math.PI * 329.63 * phase / sampleRate) * 0.2
                                c
                            }
                        }

                        phase += 1.0
                        buffer[i] = (sample * vol * Short.MAX_VALUE).toInt().coerceIn(Short.MIN_VALUE.toInt(), Short.MAX_VALUE.toInt()).toShort()
                    }
                    track.write(buffer, 0, buffer.size)
                }
            } catch (e: Exception) {
                Log.e("AudioEngine", "Ambient loop error: ${e.message}")
            }
        }
    }

    private fun startMusicLoop(musicKey: String, combat: CombatState) {
        musicJob?.cancel()
        musicJob = scope.launch {
            try {
                val minBufSize = AudioTrack.getMinBufferSize(
                    sampleRate,
                    AudioFormat.CHANNEL_OUT_MONO,
                    AudioFormat.ENCODING_PCM_16BIT
                )
                val track = AudioTrack.Builder()
                    .setAudioAttributes(
                        AudioAttributes.Builder()
                            .setUsage(AudioAttributes.USAGE_MEDIA)
                            .setContentType(AudioAttributes.CONTENT_TYPE_MUSIC)
                            .build()
                    )
                    .setAudioFormat(
                        AudioFormat.Builder()
                            .setEncoding(AudioFormat.ENCODING_PCM_16BIT)
                            .setSampleRate(sampleRate)
                            .setChannelMask(AudioFormat.CHANNEL_OUT_MONO)
                            .build()
                    )
                    .setBufferSizeInBytes(minBufSize * 2)
                    .setTransferMode(AudioTrack.MODE_STREAM)
                    .build()

                track.play()
                musicTrack = track

                val buffer = ShortArray(1024)
                var phase = 0.0
                var beatCounter = 0

                // Notes frequencies (A minor RPG lute/celestial tuning)
                val notes = doubleArrayOf(220.0, 261.63, 329.63, 392.00, 440.0, 523.25)

                while (isActive && _isPlaying.value) {
                    val vol = _musicVolume.value * _masterVolume.value
                    val tempoMultiplier = when (combat) {
                        CombatState.EXPLORATION -> 1.0
                        CombatState.TENSION -> 1.4
                        CombatState.COMBAT -> 2.2
                    }

                    for (i in buffer.indices) {
                        beatCounter++
                        val currentStep = (beatCounter / (sampleRate / (4 * tempoMultiplier)).toInt()) % 8

                        val noteFreq = notes[currentStep.toInt() % notes.size]

                        var melody = sin(2.0 * Math.PI * noteFreq * phase / sampleRate) * 0.3

                        // Combat Percussion Layer
                        var percussion = 0.0
                        if (combat == CombatState.COMBAT || combat == CombatState.TENSION) {
                            val beatTime = beatCounter % (sampleRate / (2 * tempoMultiplier)).toInt()
                            if (beatTime < 2000) { // Heavy drum strike
                                val drumEnv = (2000 - beatTime) / 2000.0
                                val drumPitch = 80.0 * drumEnv
                                percussion = sin(2.0 * Math.PI * drumPitch * phase / sampleRate) * drumEnv * (if (combat == CombatState.COMBAT) 0.8 else 0.4)
                            }
                        }

                        val sample = melody + percussion
                        phase += 1.0
                        buffer[i] = (sample * vol * Short.MAX_VALUE).toInt().coerceIn(Short.MIN_VALUE.toInt(), Short.MAX_VALUE.toInt()).toShort()
                    }
                    track.write(buffer, 0, buffer.size)
                }
            } catch (e: Exception) {
                Log.e("AudioEngine", "Music loop error: ${e.message}")
            }
        }
    }

    // Play Instant SFX Pad (Sword, Thunder, Magic, Door, Roar, Dice)
    fun playSfx(sfxType: String) {
        scope.launch {
            try {
                val minBufSize = AudioTrack.getMinBufferSize(
                    sampleRate,
                    AudioFormat.CHANNEL_OUT_MONO,
                    AudioFormat.ENCODING_PCM_16BIT
                )
                val track = AudioTrack.Builder()
                    .setAudioAttributes(
                        AudioAttributes.Builder()
                            .setUsage(AudioAttributes.USAGE_GAME)
                            .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                            .build()
                    )
                    .setAudioFormat(
                        AudioFormat.Builder()
                            .setEncoding(AudioFormat.ENCODING_PCM_16BIT)
                            .setSampleRate(sampleRate)
                            .setChannelMask(AudioFormat.CHANNEL_OUT_MONO)
                            .build()
                    )
                    .setBufferSizeInBytes(minBufSize * 2)
                    .setTransferMode(AudioTrack.MODE_STATIC)
                    .build()

                val sfxLength = (sampleRate * 0.8).toInt()
                val sfxBuffer = ShortArray(sfxLength)
                var phase = 0.0

                for (i in sfxBuffer.indices) {
                    val progress = i.toDouble() / sfxLength
                    val env = 1.0 - progress

                    val sample = when (sfxType) {
                        "SWORD" -> {
                            // Metallic slice FM
                            val freq = 1200.0 - progress * 800.0
                            val fm = sin(2.0 * Math.PI * freq * phase / sampleRate) * env
                            val metallic = (Math.random() * 2 - 1) * 0.3 * env
                            fm + metallic
                        }
                        "THUNDER" -> {
                            // Bass explosion
                            val freq = 60.0 * env
                            val bass = sin(2.0 * Math.PI * freq * phase / sampleRate) * env * 0.9
                            val rumble = (Math.random() * 2 - 1) * env * 0.6
                            bass + rumble
                        }
                        "MAGIC" -> {
                            // Shimmer chime
                            val f1 = 880.0 + sin(progress * 20.0) * 200.0
                            val f2 = 1320.0
                            val chime = (sin(2.0 * Math.PI * f1 * phase / sampleRate) + sin(2.0 * Math.PI * f2 * phase / sampleRate)) * 0.4 * env
                            chime
                        }
                        "DOOR" -> {
                            // Creak
                            val f = 150.0 + sin(progress * 30.0) * 80.0
                            sin(2.0 * Math.PI * f * phase / sampleRate) * 0.5 * env
                        }
                        "ROAR" -> {
                            // Monster growl
                            val growl = sin(2.0 * Math.PI * 70.0 * phase / sampleRate) * 0.6
                            val roughness = (Math.random() * 2 - 1) * 0.5
                            (growl + roughness) * env
                        }
                        else -> { // DICE
                            val click = if (i % 2000 < 200) (Math.random() * 2 - 1) * 0.7 else 0.0
                            click * env
                        }
                    }

                    phase += 1.0
                    sfxBuffer[i] = (sample * _masterVolume.value * Short.MAX_VALUE).toInt().coerceIn(Short.MIN_VALUE.toInt(), Short.MAX_VALUE.toInt()).toShort()
                }

                track.write(sfxBuffer, 0, sfxBuffer.size)
                track.play()
            } catch (e: Exception) {
                Log.e("AudioEngine", "SFX error: ${e.message}")
            }
        }
    }

    fun release() {
        ambientJob?.cancel()
        musicJob?.cancel()
        ambientTrack?.stop()
        ambientTrack?.release()
        musicTrack?.stop()
        musicTrack?.release()
    }
}
