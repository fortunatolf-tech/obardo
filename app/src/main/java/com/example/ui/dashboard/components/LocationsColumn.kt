package com.example.ui.dashboard.components

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
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Castle
import androidx.compose.material.icons.filled.ChevronRight
import androidx.compose.material.icons.filled.Explore
import androidx.compose.material.icons.filled.Language
import androidx.compose.material.icons.filled.MusicNote
import androidx.compose.material.icons.filled.Place
import androidx.compose.material.icons.filled.VolumeUp
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.DropdownMenu
import androidx.compose.material3.DropdownMenuItem
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
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.data.model.LocationEntity
import com.example.data.model.WorldEntity
import com.example.ui.theme.DarkSurface
import com.example.ui.theme.DarkSurfaceVariant
import com.example.ui.theme.GoldContainer
import com.example.ui.theme.GoldLight
import com.example.ui.theme.GoldPrimary
import com.example.ui.theme.OutlineGold
import com.example.ui.theme.PurpleSecondary

@Composable
fun LocationsColumn(
    worlds: List<WorldEntity>,
    selectedWorldId: Long,
    locations: List<LocationEntity>,
    selectedLocation: LocationEntity?,
    onSelectWorld: (Long) -> Unit,
    onSelectLocation: (LocationEntity) -> Unit,
    onAddLocation: (String, String, String, String, String) -> Unit,
    onAddWorld: (String, String, String) -> Unit,
    modifier: Modifier = Modifier
) {
    var showAddLocationDialog by remember { mutableStateOf(false) }
    var showAddWorldDialog by remember { mutableStateOf(false) }
    var showWorldDropdown by remember { mutableStateOf(false) }

    val currentWorld = worlds.find { it.id == selectedWorldId } ?: worlds.firstOrNull()

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
                    imageVector = Icons.Default.Explore,
                    contentDescription = "Worlds",
                    tint = GoldPrimary,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(6.dp))
                Text(
                    text = "Ecos & Locais",
                    style = MaterialTheme.typography.titleMedium.copy(
                        fontWeight = FontWeight.Bold,
                        color = GoldLight
                    )
                )
            }
            IconButton(
                onClick = { showAddLocationDialog = true },
                modifier = Modifier.testTag("add_location_button")
            ) {
                Icon(
                    imageVector = Icons.Default.Add,
                    contentDescription = "Novo Local",
                    tint = GoldPrimary
                )
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        // World Selector Card
        Card(
            colors = CardDefaults.cardColors(containerColor = DarkSurfaceVariant),
            shape = RoundedCornerShape(10.dp),
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, OutlineGold, RoundedCornerShape(10.dp))
                .clickable { showWorldDropdown = true }
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(12.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        imageVector = Icons.Default.Language,
                        contentDescription = "Campanha",
                        tint = PurpleSecondary,
                        modifier = Modifier.size(20.dp)
                    )
                    Spacer(modifier = Modifier.width(8.dp))
                    Column {
                        Text(
                            text = currentWorld?.name ?: "Selecione o Mundo",
                            style = MaterialTheme.typography.bodyMedium.copy(
                                fontWeight = FontWeight.Bold,
                                color = Color.White
                            ),
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis
                        )
                        Text(
                            text = currentWorld?.system ?: "Sistema RPG",
                            style = MaterialTheme.typography.labelSmall.copy(color = GoldPrimary)
                        )
                    }
                }
                Icon(
                    imageVector = Icons.Default.ChevronRight,
                    contentDescription = "Dropdown",
                    tint = Color.Gray
                )
            }

            DropdownMenu(
                expanded = showWorldDropdown,
                onDismissRequest = { showWorldDropdown = false },
                modifier = Modifier.background(DarkSurfaceVariant)
            ) {
                worlds.forEach { world ->
                    DropdownMenuItem(
                        text = {
                            Text(
                                text = "${world.name} (${world.system})",
                                color = if (world.id == selectedWorldId) GoldPrimary else Color.White
                            )
                        },
                        onClick = {
                            onSelectWorld(world.id)
                            showWorldDropdown = false
                        }
                    )
                }
                DropdownMenuItem(
                    text = {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Icon(Icons.Default.Add, contentDescription = null, tint = GoldPrimary)
                            Spacer(modifier = Modifier.width(6.dp))
                            Text("+ Novo Mundo / Campanha", color = GoldPrimary, fontWeight = FontWeight.Bold)
                        }
                    },
                    onClick = {
                        showWorldDropdown = false
                        showAddWorldDialog = true
                    }
                )
            }
        }

        Spacer(modifier = Modifier.height(12.dp))

        Text(
            text = "ÁRVORE NARRATIVA DE LOCAIS",
            style = MaterialTheme.typography.labelSmall.copy(
                color = Color.Gray,
                letterSpacing = 1.sp,
                fontWeight = FontWeight.SemiBold
            )
        )

        Spacer(modifier = Modifier.height(6.dp))

        // Locations List
        LazyColumn(
            verticalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier.weight(1f)
        ) {
            items(locations) { location ->
                val isSelected = selectedLocation?.id == location.id
                LocationCardItem(
                    location = location,
                    isSelected = isSelected,
                    onClick = { onSelectLocation(location) }
                )
            }
        }
    }

    // Add Location Dialog
    if (showAddLocationDialog) {
        AddLocationDialog(
            onDismiss = { showAddLocationDialog = false },
            onConfirm = { name, parent, desc, ambient, music ->
                onAddLocation(name, parent, desc, ambient, music)
                showAddLocationDialog = false
            }
        )
    }

    // Add World Dialog
    if (showAddWorldDialog) {
        AddWorldDialog(
            onDismiss = { showAddWorldDialog = false },
            onConfirm = { name, system, desc ->
                onAddWorld(name, system, desc)
                showAddWorldDialog = false
            }
        )
    }
}

@Composable
fun LocationCardItem(
    location: LocationEntity,
    isSelected: Boolean,
    onClick: () -> Unit
) {
    val bgBrush = if (isSelected) {
        Brush.horizontalGradient(listOf(GoldContainer, DarkSurfaceVariant))
    } else {
        Brush.horizontalGradient(listOf(DarkSurfaceVariant, DarkSurfaceVariant))
    }

    val borderColor = if (isSelected) GoldPrimary else OutlineGold

    Box(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(8.dp))
            .background(bgBrush)
            .border(if (isSelected) 1.5.dp else 0.5.dp, borderColor, RoundedCornerShape(8.dp))
            .clickable { onClick() }
            .padding(10.dp)
            .testTag("location_item_${location.id}")
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween,
            modifier = Modifier.fillMaxWidth()
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                modifier = Modifier.weight(1f)
            ) {
                Icon(
                    imageVector = if (isSelected) Icons.Default.Place else Icons.Default.Castle,
                    contentDescription = null,
                    tint = if (isSelected) GoldPrimary else Color.Gray,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                Column {
                    Text(
                        text = location.name,
                        style = MaterialTheme.typography.bodyMedium.copy(
                            fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal,
                            color = if (isSelected) GoldLight else Color.White
                        ),
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                    if (location.parentLocation.isNotBlank()) {
                        Text(
                            text = location.parentLocation,
                            style = MaterialTheme.typography.labelSmall.copy(color = Color.Gray),
                            maxLines = 1
                        )
                    }
                }
            }

            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(
                    imageVector = Icons.Default.VolumeUp,
                    contentDescription = "Áudio Ativo",
                    tint = if (isSelected) PurpleSecondary else Color.DarkGray,
                    modifier = Modifier.size(16.dp)
                )
                Spacer(modifier = Modifier.width(4.dp))
                Icon(
                    imageVector = Icons.Default.MusicNote,
                    contentDescription = "Trilha",
                    tint = if (isSelected) GoldPrimary else Color.DarkGray,
                    modifier = Modifier.size(16.dp)
                )
            }
        }
    }
}

@Composable
fun AddLocationDialog(
    onDismiss: () -> Unit,
    onConfirm: (String, String, String, String, String) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var parent by remember { mutableStateOf("") }
    var description by remember { mutableStateOf("") }
    var ambientKey by remember { mutableStateOf("RAIN") }
    var musicKey by remember { mutableStateOf("EXPLORATION") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = DarkSurface,
        title = { Text("📜 Criar Novo Local/Cena", color = GoldPrimary, fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Nome do Local (ex: Masmorra do Dragão)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth().testTag("new_location_name_input")
                )
                OutlinedTextField(
                    value = parent,
                    onValueChange = { parent = it },
                    label = { Text("Região / Pai (ex: Montanha da Perdição)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = description,
                    onValueChange = { description = it },
                    label = { Text("Descrição Narrativa Inicial") },
                    modifier = Modifier.fillMaxWidth().height(80.dp)
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { if (name.isNotBlank()) onConfirm(name, parent, description, ambientKey, musicKey) },
                colors = ButtonDefaults.buttonColors(containerColor = GoldPrimary)
            ) {
                Text("Criar Local", color = Color.Black, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancelar", color = Color.Gray)
            }
        }
    )
}

@Composable
fun AddWorldDialog(
    onDismiss: () -> Unit,
    onConfirm: (String, String, String) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var system by remember { mutableStateOf("D&D 5e") }
    var description by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = DarkSurface,
        title = { Text("🌐 Novo Mundo / Campanha", color = GoldPrimary, fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Nome da Campanha (ex: Ordem Paranormal)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = system,
                    onValueChange = { system = it },
                    label = { Text("Sistema (Tormenta20, D&D 5e, Vampiro)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = description,
                    onValueChange = { description = it },
                    label = { Text("Resumo da Lore do Mundo") },
                    modifier = Modifier.fillMaxWidth().height(80.dp)
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { if (name.isNotBlank()) onConfirm(name, system, description) },
                colors = ButtonDefaults.buttonColors(containerColor = GoldPrimary)
            ) {
                Text("Criar Mundo", color = Color.Black, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar", color = Color.Gray) }
        }
    )
}
