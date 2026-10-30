package com.example.ui.dashboard.components

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
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.AutoAwesome
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.FormatQuote
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Mic
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Psychology
import androidx.compose.material.icons.filled.Send
import androidx.compose.material.icons.filled.Shield
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.testTag
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.data.model.LocationEntity
import com.example.data.model.NpcEntity
import com.example.data.model.VoiceChronicleEntity
import com.example.ui.theme.CombatRed
import com.example.ui.theme.DarkBackground
import com.example.ui.theme.DarkSurface
import com.example.ui.theme.DarkSurfaceVariant
import com.example.ui.theme.GoldContainer
import com.example.ui.theme.GoldLight
import com.example.ui.theme.GoldPrimary
import com.example.ui.theme.OutlineGold
import com.example.ui.theme.PurpleContainer
import com.example.ui.theme.PurpleLight
import com.example.ui.theme.PurpleSecondary

import androidx.compose.ui.layout.ContentScale
import coil.compose.AsyncImage

@Composable
fun SceneWorkshopColumn(
    location: LocationEntity?,
    npcs: List<NpcEntity>,
    chronicles: List<VoiceChronicleEntity>,
    aiDescription: String,
    isAiLoading: Boolean,
    onDescribeSceneAi: () -> Unit,
    onGenerateSecretAi: () -> Unit,
    onSaveVoiceNote: (String) -> Unit,
    onAddNpc: (String, String, String, String) -> Unit,
    onDeleteNpc: (Long) -> Unit,
    modifier: Modifier = Modifier
) {
    var showSecret by remember { mutableStateOf(false) }
    var showAddNpcDialog by remember { mutableStateOf(false) }
    var voiceInputText by remember { mutableStateOf("") }

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(DarkBackground)
            .padding(12.dp)
    ) {
        if (location == null) {
            Box(
                contentAlignment = Alignment.Center,
                modifier = Modifier.fillMaxSize()
            ) {
                Text("Selecione um Local para Abrir o Ateliê da Cena", color = Color.Gray)
            }
            return
        }

        // Header Banner Card
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurface),
            shape = RoundedCornerShape(12.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, OutlineGold, RoundedCornerShape(12.dp))
        ) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
            ) {
                if (location.imageUrl.isNotBlank()) {
                    AsyncImage(
                        model = location.imageUrl,
                        contentDescription = location.name,
                        contentScale = ContentScale.Crop,
                        modifier = Modifier
                            .matchParentSize()
                            .clip(RoundedCornerShape(12.dp))
                    )
                }

                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(
                            Brush.verticalGradient(
                                listOf(
                                    Color.Black.copy(alpha = 0.75f),
                                    Color.Black.copy(alpha = 0.92f)
                                )
                            )
                        )
                        .padding(14.dp)
                ) {
                    Column {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Surface(
                                color = GoldContainer,
                                shape = RoundedCornerShape(6.dp),
                                border = androidx.compose.foundation.BorderStroke(1.dp, GoldPrimary)
                            ) {
                                Text(
                                    text = location.parentLocation.ifBlank { "CENA ATIVA" },
                                    style = MaterialTheme.typography.labelSmall.copy(
                                        color = GoldLight,
                                        fontWeight = FontWeight.Bold
                                    ),
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                                )
                            }

                            IconButton(
                                onClick = onDescribeSceneAi,
                                enabled = !isAiLoading,
                                modifier = Modifier.testTag("ai_describe_scene_button")
                            ) {
                                if (isAiLoading) {
                                    CircularProgressIndicator(modifier = Modifier.size(20.dp), color = GoldPrimary)
                                } else {
                                    Row(
                                        verticalAlignment = Alignment.CenterVertically,
                                        modifier = Modifier
                                            .background(PurpleSecondary, RoundedCornerShape(20.dp))
                                            .padding(horizontal = 10.dp, vertical = 4.dp)
                                    ) {
                                        Icon(Icons.Default.AutoAwesome, contentDescription = null, tint = Color.White, modifier = Modifier.size(16.dp))
                                        Spacer(modifier = Modifier.width(4.dp))
                                        Text("Descrever Cena (IA)", style = MaterialTheme.typography.labelMedium.copy(color = Color.White, fontWeight = FontWeight.Bold))
                                    }
                                }
                            }
                        }

                        Spacer(modifier = Modifier.height(6.dp))

                        Text(
                            text = location.name,
                            style = MaterialTheme.typography.headlineSmall.copy(
                                color = GoldLight,
                                fontWeight = FontWeight.Bold
                            )
                        )

                        Spacer(modifier = Modifier.height(8.dp))

                        Text(
                            text = aiDescription.ifBlank { location.description },
                            style = MaterialTheme.typography.bodyMedium.copy(
                                color = Color(0xFFE2E8F0),
                                lineHeight = 20.sp
                            )
                        )
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Secrets Bar
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
            shape = RoundedCornerShape(10.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(10.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.weight(1f)) {
                    Icon(Icons.Default.Lock, contentDescription = null, tint = CombatRed, modifier = Modifier.size(20.dp))
                    Spacer(modifier = Modifier.width(8.dp))
                    Column {
                        Text("Segredos do Mestre (Oculto dos Jogadores)", style = MaterialTheme.typography.labelMedium.copy(color = GoldPrimary, fontWeight = FontWeight.Bold))
                        if (showSecret) {
                            Text(
                                text = location.secrets.ifBlank { "Nenhum segredo definido." },
                                style = MaterialTheme.typography.bodySmall.copy(color = Color.LightGray)
                            )
                        } else {
                            Text("••••••••••••••••••••••••••••••••••••", style = MaterialTheme.typography.bodySmall.copy(color = Color.Gray))
                        }
                    }
                }

                Row {
                    IconButton(onClick = { showSecret = !showSecret }) {
                        Icon(
                            imageVector = if (showSecret) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                            contentDescription = "Mostrar Segredo",
                            tint = Color.LightGray
                        )
                    }
                    IconButton(onClick = onGenerateSecretAi) {
                        Icon(Icons.Default.Psychology, contentDescription = "Gerar Segredo IA", tint = PurpleSecondary)
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(10.dp))

        // Main Scrollable Area for NPCs and Voice Chronicles
        LazyColumn(
            verticalArrangement = Arrangement.spacedBy(10.dp),
            modifier = Modifier.weight(1f)
        ) {
            // NPCs Header
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.Person, contentDescription = null, tint = GoldPrimary, modifier = Modifier.size(18.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text(
                            text = "NPCs PRESENTES NA CENA (${npcs.size})",
                            style = MaterialTheme.typography.labelSmall.copy(
                                color = GoldLight,
                                fontWeight = FontWeight.Bold,
                                letterSpacing = 1.sp
                            )
                        )
                    }

                    TextButton(
                        onClick = { showAddNpcDialog = true },
                        modifier = Modifier.testTag("add_npc_button")
                    ) {
                        Text("+ Adicionar NPC", color = GoldPrimary, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            if (npcs.isEmpty()) {
                item {
                    Text(
                        text = "Nenhum NPC registrado neste local ainda.",
                        style = MaterialTheme.typography.bodySmall.copy(color = Color.Gray),
                        modifier = Modifier.padding(vertical = 4.dp)
                    )
                }
            } else {
                items(npcs) { npc ->
                    NpcCardItem(npc = npc, onDelete = { onDeleteNpc(npc.id) })
                }
            }

            // Voice Chronicles Section
            item {
                Spacer(modifier = Modifier.height(8.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Mic, contentDescription = null, tint = PurpleSecondary, modifier = Modifier.size(18.dp))
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "CRÔNICAS DE SESSÃO POR VOZ (IA)",
                        style = MaterialTheme.typography.labelSmall.copy(
                            color = PurpleLight,
                            fontWeight = FontWeight.Bold,
                            letterSpacing = 1.sp
                        )
                    )
                }
            }

            if (chronicles.isEmpty()) {
                item {
                    Text(
                        text = "Nenhuma nota de voz gravada para este local.",
                        style = MaterialTheme.typography.bodySmall.copy(color = Color.Gray)
                    )
                }
            } else {
                items(chronicles) { chronicle ->
                    ChronicleCardItem(chronicle = chronicle)
                }
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        // Voice Note Input Box (O Bardo Voice Recorder Simulation)
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
            shape = RoundedCornerShape(12.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, OutlineGold, RoundedCornerShape(12.dp))
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(8.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                OutlinedTextField(
                    value = voiceInputText,
                    onValueChange = { voiceInputText = it },
                    placeholder = { Text("Fale ou digite o evento rápido...", fontSize = 13.sp) },
                    modifier = Modifier
                        .weight(1f)
                        .testTag("voice_note_input"),
                    singleLine = true
                )

                Spacer(modifier = Modifier.width(6.dp))

                IconButton(
                    onClick = {
                        if (voiceInputText.isNotBlank()) {
                            onSaveVoiceNote(voiceInputText)
                            voiceInputText = ""
                        }
                    },
                    modifier = Modifier
                        .clip(CircleShape)
                        .background(GoldPrimary)
                        .testTag("save_voice_note_button")
                ) {
                    Icon(
                        imageVector = Icons.Default.Send,
                        contentDescription = "Salvar Nota",
                        tint = Color.Black
                    )
                }
            }
        }
    }

    if (showAddNpcDialog) {
        AddNpcDialog(
            onDismiss = { showAddNpcDialog = false },
            onConfirm = { name, role, notes, quote ->
                onAddNpc(name, role, notes, quote)
                showAddNpcDialog = false
            }
        )
    }
}

@Composable
fun NpcCardItem(npc: NpcEntity, onDelete: () -> Unit) {
    Card(
        colors = CardDefaults.cardColors(containerColor = DarkSurface),
        shape = RoundedCornerShape(10.dp),
        modifier = Modifier
            .fillMaxWidth()
            .border(0.5.dp, OutlineGold, RoundedCornerShape(10.dp))
    ) {
        Column(modifier = Modifier.padding(10.dp)) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(32.dp)
                            .clip(CircleShape)
                            .background(GoldContainer),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(npc.name.take(1).uppercase(), color = GoldPrimary, fontWeight = FontWeight.Bold)
                    }
                    Spacer(modifier = Modifier.width(8.dp))
                    Column {
                        Text(npc.name, style = MaterialTheme.typography.bodyMedium.copy(fontWeight = FontWeight.Bold, color = GoldLight))
                        Text(npc.role, style = MaterialTheme.typography.labelSmall.copy(color = PurpleLight))
                    }
                }

                Row(verticalAlignment = Alignment.CenterVertically) {
                    Surface(
                        color = DarkSurfaceVariant,
                        shape = RoundedCornerShape(4.dp)
                    ) {
                        Row(modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp), verticalAlignment = Alignment.CenterVertically) {
                            Icon(Icons.Default.Shield, contentDescription = null, tint = GoldPrimary, modifier = Modifier.size(12.dp))
                            Spacer(modifier = Modifier.width(2.dp))
                            Text("CA ${npc.ac} | HP ${npc.hp}", style = MaterialTheme.typography.labelSmall.copy(color = Color.White))
                        }
                    }
                    IconButton(onClick = onDelete) {
                        Icon(Icons.Default.Delete, contentDescription = "Deletar NPC", tint = Color.Gray, modifier = Modifier.size(18.dp))
                    }
                }
            }

            if (npc.quote.isNotBlank()) {
                Spacer(modifier = Modifier.height(4.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.FormatQuote, contentDescription = null, tint = GoldPrimary, modifier = Modifier.size(16.dp))
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(
                        text = "\"${npc.quote}\"",
                        style = MaterialTheme.typography.bodySmall.copy(color = Color(0xFFFFE082), fontStyle = androidx.compose.ui.text.font.FontStyle.Italic)
                    )
                }
            }

            if (npc.notes.isNotBlank()) {
                Spacer(modifier = Modifier.height(4.dp))
                Text(npc.notes, style = MaterialTheme.typography.bodySmall.copy(color = Color.LightGray))
            }
        }
    }
}

@Composable
fun ChronicleCardItem(chronicle: VoiceChronicleEntity) {
    Card(
        colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
        shape = RoundedCornerShape(10.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(10.dp)) {
            Text(
                text = chronicle.formattedLore,
                style = MaterialTheme.typography.bodySmall.copy(color = Color(0xFFF1F5F9), lineHeight = 18.sp)
            )
        }
    }
}

@Composable
fun AddNpcDialog(
    onDismiss: () -> Unit,
    onConfirm: (String, String, String, String) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var role by remember { mutableStateOf("") }
    var notes by remember { mutableStateOf("") }
    var quote by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = DarkSurface,
        title = { Text("👤 Novo NPC na Cena", color = GoldPrimary, fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Nome do NPC (ex: Strahd von Zarovich)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth().testTag("new_npc_name_input")
                )
                OutlinedTextField(
                    value = role,
                    onValueChange = { role = it },
                    label = { Text("Papel / Função (ex: Vilão Principal)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = quote,
                    onValueChange = { quote = it },
                    label = { Text("Citação de Impacto / Frase Mão") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = notes,
                    onValueChange = { notes = it },
                    label = { Text("Anotações de Comportamento") },
                    modifier = Modifier.fillMaxWidth().height(80.dp)
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { if (name.isNotBlank()) onConfirm(name, role, notes, quote) },
                colors = ButtonDefaults.buttonColors(containerColor = GoldPrimary)
            ) {
                Text("Adicionar NPC", color = Color.Black, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar", color = Color.Gray) }
        }
    )
}
