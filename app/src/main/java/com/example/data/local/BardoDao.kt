package com.example.data.local

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.example.data.model.LocationEntity
import com.example.data.model.NpcEntity
import com.example.data.model.SfxPadEntity
import com.example.data.model.VoiceChronicleEntity
import com.example.data.model.WorldEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface BardoDao {
    // Worlds
    @Query("SELECT * FROM worlds ORDER BY id ASC")
    fun getAllWorlds(): Flow<List<WorldEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertWorld(world: WorldEntity): Long

    // Locations
    @Query("SELECT * FROM locations WHERE worldId = :worldId ORDER BY id ASC")
    fun getLocationsForWorld(worldId: Long): Flow<List<LocationEntity>>

    @Query("SELECT * FROM locations WHERE isCurrentScene = 1 LIMIT 1")
    fun getCurrentSceneLocation(): Flow<LocationEntity?>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertLocation(location: LocationEntity): Long

    @Update
    suspend fun updateLocation(location: LocationEntity)

    @Query("UPDATE locations SET isCurrentScene = CASE WHEN id = :locationId THEN 1 ELSE 0 END")
    suspend fun setCurrentScene(locationId: Long)

    // NPCs
    @Query("SELECT * FROM npcs WHERE locationId = :locationId ORDER BY id ASC")
    fun getNpcsForLocation(locationId: Long): Flow<List<NpcEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertNpc(npc: NpcEntity): Long

    @Query("DELETE FROM npcs WHERE id = :npcId")
    suspend fun deleteNpc(npcId: Long)

    // Voice Chronicles
    @Query("SELECT * FROM voice_chronicles WHERE locationId = :locationId ORDER BY timestamp DESC")
    fun getChroniclesForLocation(locationId: Long): Flow<List<VoiceChronicleEntity>>

    @Query("SELECT * FROM voice_chronicles ORDER BY timestamp DESC")
    fun getAllChronicles(): Flow<List<VoiceChronicleEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertChronicle(chronicle: VoiceChronicleEntity): Long

    // SFX Pads
    @Query("SELECT * FROM sfx_pads")
    fun getAllSfxPads(): Flow<List<SfxPadEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertSfxPads(pads: List<SfxPadEntity>)
}
