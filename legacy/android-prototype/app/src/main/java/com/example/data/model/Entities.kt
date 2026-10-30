package com.example.data.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "worlds")
data class WorldEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val name: String,
    val system: String, // e.g. D&D 5e, Tormenta20, Ordem Paranormal
    val description: String,
    val createdAt: Long = System.currentTimeMillis()
)

@Entity(tableName = "locations")
data class LocationEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val worldId: Long,
    val name: String,
    val parentLocation: String = "",
    val description: String,
    val secrets: String = "",
    val ambientSoundKey: String = "RAIN", // RAIN, TAVERN, FIRE, DUNGEON, WIND, MYSTIC
    val musicTrackKey: String = "EXPLORATION", // EXPLORATION, MYSTERY, INVESTIGATION, DRAMA, COMBAT
    val imageUrl: String = "",
    val isCurrentScene: Boolean = false
)

@Entity(tableName = "npcs")
data class NpcEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val locationId: Long,
    val name: String,
    val role: String, // e.g. Vilão, Aliado, Informante
    val notes: String,
    val quote: String = "",
    val hp: Int = 20,
    val ac: Int = 14
)

@Entity(tableName = "voice_chronicles")
data class VoiceChronicleEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val locationId: Long,
    val rawText: String,
    val formattedLore: String,
    val timestamp: Long = System.currentTimeMillis()
)

@Entity(tableName = "sfx_pads")
data class SfxPadEntity(
    @PrimaryKey val id: String,
    val name: String,
    val keyShortcut: String,
    val soundType: String, // SWORD, THUNDER, MAGIC, DOOR, ROAR, DICE
    val iconName: String
)

enum class CombatState(val displayName: String, val level: Int) {
    EXPLORATION("Exploração", 1),
    TENSION("Tensão Narrative", 2),
    COMBAT("Combate Emocionante!", 3)
}
