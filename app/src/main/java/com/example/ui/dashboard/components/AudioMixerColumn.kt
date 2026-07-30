package com.example.ui.dashboard.components

import androidx.compose.animation.animateColorAsState
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AutoAwesome
import androidx.compose.material.icons.filled.Casino
import androidx.compose.material.icons.filled.DoorBack
import androidx.compose.material.icons.filled.FlashOn
import androidx.compose.material.icons.filled.GraphicEq
import androidx.compose.material.icons.filled.Hardware
import androidx.compose.material.icons.filled.MusicNote
import androidx.compose.material.icons.filled.Pause
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.VolumeUp
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.FilterChip
import androidx.compose.material3.FilterChipDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Slider
import androidx.compose.material3.SliderDefaults
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.testTag
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.data.model.CombatState
import com.example.data.model.SfxPadEntity
import com.example.ui.theme.CombatRed
import com.example.ui.theme.CombatRedContainer
import com.example.ui.theme.DarkSurface
import com.example.ui.theme.DarkSurfaceVariant
import com.example.ui.theme.GoldContainer
import com.example.ui.theme.GoldLight
import com.example.ui.theme.GoldPrimary
import com.example.ui.theme.OutlineGold
import com.example.ui.theme.PurpleContainer
import com.example.ui.theme.PurpleLight
import com.example.ui.theme.PurpleSecondary

@Composable
fun AudioMixerColumn(
    combatState: CombatState,
    currentAmbient: String,
    currentMusic: String,
    ambientVolume: Float,
    musicVolume: Float,
    masterVolume: Float,
    isPlaying: Boolean,
    sfxPads: List<SfxPadEntity>,
    diceResultText: String,
    onToggleCombatState: () -> Unit,
    onChangeAmbient: (String) -> Unit,
    onChangeMusic: (String) -> Unit,
    onApplyPreset: (String) -> Unit,
    onSetAmbientVolume: (Float) -> Unit,
    onSetMusicVolume: (Float) -> Unit,
    onSetMasterVolume: (Float) -> Unit,
    onTogglePlayPause: () -> Unit,
    onTriggerSfx: (String, String) -> Unit,
    onRollDice: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    val combatBgColor by animateColorAsState(
        targetValue = when (combatState) {
            CombatState.EXPLORATION -> GoldContainer
            CombatState.TENSION -> PurpleContainer
            CombatState.COMBAT -> CombatRedContainer
        }, label = "combatColor"
    )

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(DarkSurface)
            .padding(12.dp)
    ) {
        // Section Header
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(
                    imageVector = Icons.Default.GraphicEq,
                    contentDescription = "Mixer",
                    tint = GoldPrimary,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = "O Alaúde - Studio de Áudio RPG",
                    style = MaterialTheme.typography.titleMedium.copy(
                        fontWeight = FontWeight.Bold,
                        color = GoldLight
                    )
                )
            }

            IconButton(
                onClick = onTogglePlayPause,
                modifier = Modifier.testTag("play_pause_audio_button")
            ) {
                Icon(
                    imageVector = if (isPlaying) Icons.Default.Pause else Icons.Default.PlayArrow,
                    contentDescription = "Play/Pause",
                    tint = GoldPrimary,
                    modifier = Modifier.size(24.dp)
                )
            }
        }

        Spacer(modifier = Modifier.height(6.dp))

        // PRESETS RÁPIDOS DE PAISAGEM SONORA (Inspirado em Tabletop Audio, Bardfy, Pocket Bard, Syrinscape)
        Text(
            text = "PAISAGENS SONORAS PRÉ-DEFINIDAS (1-TAP)",
            style = MaterialTheme.typography.labelSmall.copy(
                color = GoldPrimary,
                letterSpacing = 1.sp,
                fontWeight = FontWeight.Bold
            )
        )

        Spacer(modifier = Modifier.height(4.dp))

        Row(
            horizontalArrangement = Arrangement.spacedBy(6.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            val presets = listOf(
                "TAVERN" to "🍺 Taverna",
                "CASTLE" to "🏰 Castelo",
                "FOREST" to "🌲 Floresta",
                "DUNGEON" to "🐉 Masmorra",
                "BATTLE" to "⚔️ Batalha",
                "MYSTIC" to "✨ Místico"
            )
            presets.take(4).forEach { (presetKey, label) ->
                Button(
                    onClick = { onApplyPreset(presetKey) },
                    colors = ButtonDefaults.buttonColors(containerColor = DarkSurfaceVariant),
                    border = androidx.compose.foundation.BorderStroke(1.dp, OutlineGold),
                    shape = RoundedCornerShape(6.dp),
                    modifier = Modifier.weight(1f).testTag("preset_button_$presetKey")
                ) {
                    Text(label, fontSize = 10.sp, color = GoldLight, fontWeight = FontWeight.Bold, maxLines = 1)
                }
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        // O CHAVEADOR DE COMBATE (Global Combat Switch Button)
        Card(
            colors = CardDefaults.cardColors(containerColor = combatBgColor),
            shape = RoundedCornerShape(12.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(2.dp, if (combatState == CombatState.COMBAT) CombatRed else GoldPrimary, RoundedCornerShape(12.dp))
                .clickable { onToggleCombatState() }
                .testTag("combat_switch_button")
        ) {
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(12.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Text(
                    text = "CHAVEADOR DE COMBATE (0-CLICKS)",
                    style = MaterialTheme.typography.labelSmall.copy(
                        color = Color.White.copy(alpha = 0.8f),
                        letterSpacing = 1.sp,
                        fontWeight = FontWeight.SemiBold
                    )
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = combatState.displayName.uppercase(),
                    style = MaterialTheme.typography.titleLarge.copy(
                        fontWeight = FontWeight.ExtraBold,
                        color = if (combatState == CombatState.COMBAT) CombatRed else GoldLight
                    ),
                    textAlign = TextAlign.Center
                )
                Spacer(modifier = Modifier.height(2.dp))
                Text(
                    text = "Clique para alternar: Exploração ➔ Tensão ➔ Combate",
                    style = MaterialTheme.typography.labelSmall.copy(color = Color.LightGray)
                )
            }
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Ambient Sound Controls
        Card(
            colors = CardDefaults.cardColors(containerColor = Color(0xFF222228)),
            shape = RoundedCornerShape(10.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            Column(modifier = Modifier.padding(10.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("Camada de Fundo (Ambiente)", style = MaterialTheme.typography.labelMedium.copy(color = GoldPrimary, fontWeight = FontWeight.Bold))
                    Text("${(ambientVolume * 100).toInt()}%", style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray))
                }

                Slider(
                    value = ambientVolume,
                    onValueChange = onSetAmbientVolume,
                    colors = SliderDefaults.colors(thumbColor = GoldPrimary, activeTrackColor = GoldPrimary)
                )

                Row(
                    horizontalArrangement = Arrangement.spacedBy(6.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    val ambientOptions = listOf("RAIN" to "🌧️ Chuva", "TAVERN" to "🍺 Taverna", "FIRE" to "🔥 Fogo", "DUNGEON" to "🏰 Masmorra", "WIND" to "💨 Vento", "MYSTIC" to "✨ Místico")
                    ambientOptions.take(3).forEach { (key, label) ->
                        FilterChip(
                            selected = currentAmbient == key,
                            onClick = { onChangeAmbient(key) },
                            label = { Text(label, fontSize = 11.sp) },
                            colors = FilterChipDefaults.filterChipColors(selectedContainerColor = GoldPrimary, selectedLabelColor = Color.Black)
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        // Music Sound Controls
        Card(
            colors = CardDefaults.cardColors(containerColor = Color(0xFF222228)),
            shape = RoundedCornerShape(10.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            Column(modifier = Modifier.padding(10.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("Camada Musical (Trilha)", style = MaterialTheme.typography.labelMedium.copy(color = PurpleLight, fontWeight = FontWeight.Bold))
                    Text("${(musicVolume * 100).toInt()}%", style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray))
                }

                Slider(
                    value = musicVolume,
                    onValueChange = onSetMusicVolume,
                    colors = SliderDefaults.colors(thumbColor = PurpleSecondary, activeTrackColor = PurpleSecondary)
                )

                Row(
                    horizontalArrangement = Arrangement.spacedBy(6.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    val musicOptions = listOf("EXPLORATION" to "🗺️ Exploração", "MYSTERY" to "🔮 Mistério", "INVESTIGATION" to "🔍 Investigação")
                    musicOptions.forEach { (key, label) ->
                        FilterChip(
                            selected = currentMusic == key,
                            onClick = { onChangeMusic(key) },
                            label = { Text(label, fontSize = 11.sp) },
                            colors = FilterChipDefaults.filterChipColors(selectedContainerColor = PurpleSecondary, selectedLabelColor = Color.White)
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Pad de Ecos (SFX Pad)
        Text(
            text = "PAD DE ECOS (EFEITOS DE DISPARO)",
            style = MaterialTheme.typography.labelSmall.copy(
                color = Color.Gray,
                letterSpacing = 1.sp,
                fontWeight = FontWeight.SemiBold
            )
        )

        Spacer(modifier = Modifier.height(6.dp))

        LazyVerticalGrid(
            columns = GridCells.Fixed(3),
            horizontalArrangement = Arrangement.spacedBy(6.dp),
            verticalArrangement = Arrangement.spacedBy(6.dp),
            modifier = Modifier.weight(1f)
        ) {
            items(sfxPads) { pad ->
                SfxPadButton(pad = pad, onClick = { onTriggerSfx(pad.soundType, pad.name) })
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        // Quick Dice Roller Section
        Card(
            colors = CardDefaults.cardColors(containerColor = GoldContainer),
            shape = RoundedCornerShape(10.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, OutlineGold, RoundedCornerShape(10.dp))
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(8.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text("Rolagem do Mestre", style = MaterialTheme.typography.labelSmall.copy(color = GoldLight, fontWeight = FontWeight.Bold))
                    Text(
                        text = diceResultText.ifBlank { "Clique d20 para rolar!" },
                        style = MaterialTheme.typography.bodySmall.copy(color = Color.White, fontWeight = FontWeight.Bold)
                    )
                }

                Button(
                    onClick = { onRollDice(20) },
                    colors = ButtonDefaults.buttonColors(containerColor = GoldPrimary),
                    modifier = Modifier.testTag("roll_d20_button")
                ) {
                    Icon(Icons.Default.Casino, contentDescription = null, tint = Color.Black)
                    Spacer(modifier = Modifier.width(4.dp))
                    Text("d20", color = Color.Black, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun SfxPadButton(pad: SfxPadEntity, onClick: () -> Unit) {
    val icon = when (pad.soundType) {
        "SWORD" -> Icons.Default.Hardware
        "THUNDER" -> Icons.Default.FlashOn
        "MAGIC" -> Icons.Default.AutoAwesome
        "DOOR" -> Icons.Default.DoorBack
        "ROAR" -> Icons.Default.Warning
        else -> Icons.Default.Casino
    }

    Card(
        colors = CardDefaults.cardColors(containerColor = Color(0xFF26262D)),
        shape = RoundedCornerShape(8.dp),
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, OutlineGold, RoundedCornerShape(8.dp))
            .clickable { onClick() }
            .testTag("sfx_pad_${pad.id}")
    ) {
        Column(
            modifier = Modifier.padding(8.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Icon(imageVector = icon, contentDescription = null, tint = GoldPrimary, modifier = Modifier.size(20.dp))
            Spacer(modifier = Modifier.height(2.dp))
            Text(pad.name, style = MaterialTheme.typography.labelSmall.copy(color = Color.White, fontSize = 10.sp), textAlign = TextAlign.Center, maxLines = 1)
            Text("[${pad.keyShortcut}]", style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray, fontSize = 9.sp))
        }
    }
}
