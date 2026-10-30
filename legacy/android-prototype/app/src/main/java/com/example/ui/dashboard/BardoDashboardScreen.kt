package com.example.ui.dashboard

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.BoxWithConstraints
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Castle
import androidx.compose.material.icons.filled.Explore
import androidx.compose.material.icons.filled.GraphicEq
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Tab
import androidx.compose.material3.TabRow
import androidx.compose.material3.TabRowDefaults
import androidx.compose.material3.TabRowDefaults.tabIndicatorOffset
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.testTag
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.ui.BardoViewModel
import com.example.ui.dashboard.components.AudioMixerColumn
import com.example.ui.dashboard.components.LocationsColumn
import com.example.ui.dashboard.components.SceneWorkshopColumn
import com.example.ui.theme.DarkBackground
import com.example.ui.theme.DarkSurface
import com.example.ui.theme.GoldLight
import com.example.ui.theme.GoldPrimary

@Composable
fun BardoDashboardScreen(
    viewModel: BardoViewModel,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()
    val worlds by viewModel.worlds.collectAsState()
    val locations by viewModel.locations.collectAsState()
    val npcs by viewModel.npcs.collectAsState()
    val locationChronicles by viewModel.locationChronicles.collectAsState()
    val sfxPads by viewModel.sfxPads.collectAsState()

    val ambientVolume by viewModel.audioEngine.ambientVolume.collectAsState()
    val musicVolume by viewModel.audioEngine.musicVolume.collectAsState()
    val masterVolume by viewModel.audioEngine.masterVolume.collectAsState()
    val currentAmbient by viewModel.audioEngine.currentAmbient.collectAsState()
    val currentMusic by viewModel.audioEngine.currentMusic.collectAsState()
    val combatState by viewModel.audioEngine.combatState.collectAsState()
    val isPlaying by viewModel.audioEngine.isPlaying.collectAsState()

    var selectedTabIndex by remember { mutableIntStateOf(1) } // Default to Scene Workshop

    BoxWithConstraints(
        modifier = modifier
            .fillMaxSize()
            .background(DarkBackground)
    ) {
        val isWideScreen = maxWidth > 840.dp

        if (isWideScreen) {
            // Wide Screen: 3-Column Studio Layout
            Row(modifier = Modifier.fillMaxSize()) {
                LocationsColumn(
                    worlds = worlds,
                    selectedWorldId = uiState.selectedWorldId,
                    locations = locations,
                    selectedLocation = uiState.selectedLocation,
                    onSelectWorld = { viewModel.selectWorld(it) },
                    onSelectLocation = { viewModel.selectLocation(it) },
                    onAddLocation = { name, parent, desc, amb, mus -> viewModel.addLocation(name, parent, desc, amb, mus) },
                    onAddWorld = { name, sys, desc -> viewModel.addWorld(name, sys, desc) },
                    modifier = Modifier
                        .weight(1f)
                        .fillMaxHeight()
                )

                SceneWorkshopColumn(
                    location = uiState.selectedLocation,
                    npcs = npcs,
                    chronicles = locationChronicles,
                    aiDescription = uiState.aiDescription,
                    isAiLoading = uiState.isAiLoading,
                    onDescribeSceneAi = { viewModel.describeSceneWithAi() },
                    onGenerateSecretAi = { viewModel.generateSecretWithAi() },
                    onSaveVoiceNote = { viewModel.saveVoiceChronicle(it) },
                    onAddNpc = { name, role, notes, quote -> viewModel.addNpc(name, role, notes, quote) },
                    onDeleteNpc = { viewModel.deleteNpc(it) },
                    modifier = Modifier
                        .weight(1.5f)
                        .fillMaxHeight()
                )

                AudioMixerColumn(
                    combatState = combatState,
                    currentAmbient = currentAmbient,
                    currentMusic = currentMusic,
                    ambientVolume = ambientVolume,
                    musicVolume = musicVolume,
                    masterVolume = masterVolume,
                    isPlaying = isPlaying,
                    sfxPads = sfxPads,
                    diceResultText = uiState.diceResultText,
                    onToggleCombatState = { viewModel.toggleCombatState() },
                    onChangeAmbient = { viewModel.audioEngine.setAmbientSound(it) },
                    onChangeMusic = { viewModel.audioEngine.setMusicTrack(it) },
                    onApplyPreset = { viewModel.audioEngine.applyPreset(it) },
                    onSetAmbientVolume = { viewModel.audioEngine.setAmbientVolume(it) },
                    onSetMusicVolume = { viewModel.audioEngine.setMusicVolume(it) },
                    onSetMasterVolume = { viewModel.audioEngine.setMasterVolume(it) },
                    onTogglePlayPause = { viewModel.audioEngine.togglePlayPause() },
                    onTriggerSfx = { sfx, name -> viewModel.triggerSfx(sfx, name) },
                    onRollDice = { viewModel.rollDice(it) },
                    modifier = Modifier
                        .weight(1.2f)
                        .fillMaxHeight()
                )
            }
        } else {
            // Compact Screen: Tabbed Studio Layout
            Column(modifier = Modifier.fillMaxSize()) {
                TabRow(
                    selectedTabIndex = selectedTabIndex,
                    containerColor = DarkSurface,
                    contentColor = GoldPrimary,
                    indicator = { tabPositions ->
                        TabRowDefaults.SecondaryIndicator(
                            modifier = Modifier.tabIndicatorOffset(tabPositions[selectedTabIndex]),
                            color = GoldPrimary
                        )
                    },
                    modifier = Modifier.testTag("dashboard_tab_row")
                ) {
                    Tab(
                        selected = selectedTabIndex == 0,
                        onClick = { selectedTabIndex = 0 },
                        text = {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(Icons.Default.Explore, contentDescription = null, modifier = Modifier.padding(end = 4.dp))
                                Text("Locais", fontWeight = FontWeight.Bold, fontSize = 12.sp)
                            }
                        }
                    )
                    Tab(
                        selected = selectedTabIndex == 1,
                        onClick = { selectedTabIndex = 1 },
                        text = {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(Icons.Default.Castle, contentDescription = null, modifier = Modifier.padding(end = 4.dp))
                                Text("Cena ATIVA", fontWeight = FontWeight.Bold, fontSize = 12.sp)
                            }
                        }
                    )
                    Tab(
                        selected = selectedTabIndex == 2,
                        onClick = { selectedTabIndex = 2 },
                        text = {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Icon(Icons.Default.GraphicEq, contentDescription = null, modifier = Modifier.padding(end = 4.dp))
                                Text("O Alaúde", fontWeight = FontWeight.Bold, fontSize = 12.sp)
                            }
                        }
                    )
                }

                Box(modifier = Modifier.weight(1f)) {
                    when (selectedTabIndex) {
                        0 -> LocationsColumn(
                            worlds = worlds,
                            selectedWorldId = uiState.selectedWorldId,
                            locations = locations,
                            selectedLocation = uiState.selectedLocation,
                            onSelectWorld = { viewModel.selectWorld(it) },
                            onSelectLocation = {
                                viewModel.selectLocation(it)
                                selectedTabIndex = 1 // Switch automatically to Scene Workshop
                            },
                            onAddLocation = { name, parent, desc, amb, mus -> viewModel.addLocation(name, parent, desc, amb, mus) },
                            onAddWorld = { name, sys, desc -> viewModel.addWorld(name, sys, desc) }
                        )
                        1 -> SceneWorkshopColumn(
                            location = uiState.selectedLocation,
                            npcs = npcs,
                            chronicles = locationChronicles,
                            aiDescription = uiState.aiDescription,
                            isAiLoading = uiState.isAiLoading,
                            onDescribeSceneAi = { viewModel.describeSceneWithAi() },
                            onGenerateSecretAi = { viewModel.generateSecretWithAi() },
                            onSaveVoiceNote = { viewModel.saveVoiceChronicle(it) },
                            onAddNpc = { name, role, notes, quote -> viewModel.addNpc(name, role, notes, quote) },
                            onDeleteNpc = { viewModel.deleteNpc(it) }
                        )
                        2 -> AudioMixerColumn(
                            combatState = combatState,
                            currentAmbient = currentAmbient,
                            currentMusic = currentMusic,
                            ambientVolume = ambientVolume,
                            musicVolume = musicVolume,
                            masterVolume = masterVolume,
                            isPlaying = isPlaying,
                            sfxPads = sfxPads,
                            diceResultText = uiState.diceResultText,
                            onToggleCombatState = { viewModel.toggleCombatState() },
                            onChangeAmbient = { viewModel.audioEngine.setAmbientSound(it) },
                            onChangeMusic = { viewModel.audioEngine.setMusicTrack(it) },
                            onApplyPreset = { viewModel.audioEngine.applyPreset(it) },
                            onSetAmbientVolume = { viewModel.audioEngine.setAmbientVolume(it) },
                            onSetMusicVolume = { viewModel.audioEngine.setMusicVolume(it) },
                            onSetMasterVolume = { viewModel.audioEngine.setMasterVolume(it) },
                            onTogglePlayPause = { viewModel.audioEngine.togglePlayPause() },
                            onTriggerSfx = { sfx, name -> viewModel.triggerSfx(sfx, name) },
                            onRollDice = { viewModel.rollDice(it) }
                        )
                    }
                }
            }
        }
    }
}
