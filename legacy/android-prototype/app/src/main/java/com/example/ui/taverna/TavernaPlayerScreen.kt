package com.example.ui.taverna

import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.RepeatMode
import androidx.compose.animation.core.animateFloat
import androidx.compose.animation.core.infiniteRepeatable
import androidx.compose.animation.core.rememberInfiniteTransition
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
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
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Casino
import androidx.compose.material.icons.filled.GraphicEq
import androidx.compose.material.icons.filled.Language
import androidx.compose.material.icons.filled.Shield
import androidx.compose.material.icons.filled.Wifi
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.testTag
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.data.model.CombatState
import com.example.ui.BardoViewModel
import com.example.ui.theme.CombatRed
import com.example.ui.theme.DarkBackground
import com.example.ui.theme.DarkSurface
import com.example.ui.theme.DarkSurfaceVariant
import com.example.ui.theme.GoldContainer
import com.example.ui.theme.GoldLight
import com.example.ui.theme.GoldPrimary
import com.example.ui.theme.OutlineGold
import com.example.ui.theme.PurpleSecondary
import kotlin.math.sin

import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.draw.clip
import coil.compose.AsyncImage

@Composable
fun TavernaPlayerScreen(
    viewModel: BardoViewModel,
    modifier: Modifier = Modifier
) {
    val uiState by viewModel.uiState.collectAsState()
    val combatState by viewModel.audioEngine.combatState.collectAsState()
    val isPlaying by viewModel.audioEngine.isPlaying.collectAsState()

    val currentLoc = uiState.selectedLocation

    // Infinite transition for waveform
    val infiniteTransition = rememberInfiniteTransition(label = "wave")
    val wavePhase by infiniteTransition.animateFloat(
        initialValue = 0f,
        targetValue = 2f * Math.PI.toFloat(),
        animationSpec = infiniteRepeatable(
            animation = tween(1500, easing = LinearEasing),
            repeatMode = RepeatMode.Restart
        ),
        label = "wavePhase"
    )

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(DarkBackground)
            .padding(16.dp)
    ) {
        // Player Link Bar Header
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurface),
            shape = RoundedCornerShape(12.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, OutlineGold, RoundedCornerShape(12.dp))
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(12.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Language, contentDescription = null, tint = GoldPrimary, modifier = Modifier.size(20.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Column {
                        Text("A TAVERNA (Visão do Jogador - Web)", style = MaterialTheme.typography.labelSmall.copy(color = GoldPrimary, fontWeight = FontWeight.Bold))
                        Text("obardo.com.br/sala/${uiState.sessionCode}", style = MaterialTheme.typography.bodyMedium.copy(color = Color.White, fontWeight = FontWeight.Bold))
                    }
                }

                Surface(
                    color = Color(0xFF065F46),
                    shape = RoundedCornerShape(20.dp)
                ) {
                    Row(
                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(Icons.Default.Wifi, contentDescription = null, tint = Color(0xFF34D399), modifier = Modifier.size(14.dp))
                        Spacer(modifier = Modifier.width(4.dp))
                        Text("HD 320kbps Sincro", style = MaterialTheme.typography.labelSmall.copy(color = Color(0xFF34D399), fontWeight = FontWeight.Bold))
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(14.dp))

        // Scene Title & Combat State Banner
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
            shape = RoundedCornerShape(14.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.5.dp, if (combatState == CombatState.COMBAT) CombatRed else GoldPrimary, RoundedCornerShape(14.dp))
        ) {
            Box(modifier = Modifier.fillMaxWidth()) {
                if (currentLoc?.imageUrl?.isNotBlank() == true) {
                    AsyncImage(
                        model = currentLoc.imageUrl,
                        contentDescription = currentLoc.name,
                        contentScale = ContentScale.Crop,
                        modifier = Modifier
                            .matchParentSize()
                            .clip(RoundedCornerShape(14.dp))
                    )
                }

                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(
                            Brush.verticalGradient(
                                listOf(
                                    Color.Black.copy(alpha = 0.82f),
                                    Color.Black.copy(alpha = 0.95f)
                                )
                            )
                        )
                        .padding(16.dp)
                ) {
                    Column {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = currentLoc?.parentLocation ?: "MESA DO MESTRE",
                                style = MaterialTheme.typography.labelSmall.copy(color = GoldPrimary, fontWeight = FontWeight.Bold, letterSpacing = 1.sp)
                            )

                            Surface(
                                color = when (combatState) {
                                    CombatState.EXPLORATION -> GoldContainer
                                    CombatState.TENSION -> Color(0xFF3B0764)
                                    CombatState.COMBAT -> Color(0xFF450A0A)
                                },
                                shape = RoundedCornerShape(6.dp)
                            ) {
                                Text(
                                    text = combatState.displayName.uppercase(),
                                    style = MaterialTheme.typography.labelMedium.copy(
                                        color = if (combatState == CombatState.COMBAT) CombatRed else GoldLight,
                                        fontWeight = FontWeight.ExtraBold
                                    ),
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))

                        Text(
                            text = currentLoc?.name ?: "Aguardando Mestre iniciar a cena...",
                            style = MaterialTheme.typography.titleLarge.copy(color = Color.White, fontWeight = FontWeight.Bold)
                        )

                        Spacer(modifier = Modifier.height(6.dp))

                        Text(
                            text = uiState.aiDescription.ifBlank { currentLoc?.description ?: "" },
                            style = MaterialTheme.typography.bodyMedium.copy(color = Color.LightGray, lineHeight = 20.sp)
                        )

                        Spacer(modifier = Modifier.height(14.dp))

                        // WAVEFORM CANVAS VISUALIZER
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(60.dp)
                                .background(Color(0xFF0F0F12), RoundedCornerShape(8.dp))
                                .border(1.dp, Color(0xFF27272A), RoundedCornerShape(8.dp))
                        ) {
                            Canvas(modifier = Modifier.fillMaxSize()) {
                                val width = size.width
                                val height = size.height
                                val centerY = height / 2f
                                val waveColor = if (combatState == CombatState.COMBAT) Color(0xFFEF4444) else Color(0xFFD4AF37)

                                for (x in 0..width.toInt() step 6) {
                                    val normX = x / width
                                    val amp = if (isPlaying) (sin(normX * 12f + wavePhase) * 20f + sin(normX * 6f - wavePhase) * 10f) else 2f
                                    drawLine(
                                        color = waveColor.copy(alpha = 0.8f),
                                        start = Offset(x.toFloat(), centerY - amp),
                                        end = Offset(x.toFloat(), centerY + amp),
                                        strokeWidth = 3f
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(14.dp))

        // Player Dice Roller Bar
        Text(
            text = "DADOS DE MESA DOS JOGADORES",
            style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray, letterSpacing = 1.sp, fontWeight = FontWeight.Bold)
        )

        Spacer(modifier = Modifier.height(6.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            val diceSides = listOf(4, 6, 8, 10, 12, 20, 100)
            diceSides.forEach { sides ->
                Button(
                    onClick = { viewModel.rollDice(sides) },
                    colors = ButtonDefaults.buttonColors(containerColor = GoldContainer),
                    shape = RoundedCornerShape(8.dp),
                    modifier = Modifier
                        .weight(1f)
                        .border(1.dp, OutlineGold, RoundedCornerShape(8.dp))
                        .testTag("player_dice_d$sides")
                ) {
                    Text("d$sides", color = GoldLight, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                }
            }
        }

        if (uiState.diceResultText.isNotBlank()) {
            Spacer(modifier = Modifier.height(6.dp))
            Card(
                colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
                modifier = Modifier.fillMaxWidth()
            ) {
                Row(
                    modifier = Modifier.padding(10.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(Icons.Default.Casino, contentDescription = null, tint = GoldPrimary)
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(uiState.diceResultText, style = MaterialTheme.typography.bodyMedium.copy(color = Color.White, fontWeight = FontWeight.Bold))
                }
            }
        }

        Spacer(modifier = Modifier.height(14.dp))

        // Live Feed Log
        Text(
            text = "FEED EM TEMPO REAL DA SESSÃO (WEBSOCKETS)",
            style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray, letterSpacing = 1.sp, fontWeight = FontWeight.Bold)
        )

        Spacer(modifier = Modifier.height(6.dp))

        LazyColumn(
            verticalArrangement = Arrangement.spacedBy(6.dp),
            modifier = Modifier.weight(1f)
        ) {
            items(uiState.livePlayerLogs) { logMsg ->
                Card(
                    colors = CardDefaults.cardColors(containerColor = DarkSurface),
                    shape = RoundedCornerShape(8.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text(
                        text = logMsg,
                        style = MaterialTheme.typography.bodySmall.copy(color = Color(0xFFCBD5E1)),
                        modifier = Modifier.padding(10.dp)
                    )
                }
            }
        }
    }
}
