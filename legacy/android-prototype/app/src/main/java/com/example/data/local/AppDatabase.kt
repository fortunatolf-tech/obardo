package com.example.data.local

import android.content.Context
import androidx.room.Database
import androidx.room.Room
import androidx.room.RoomDatabase
import com.example.data.model.LocationEntity
import com.example.data.model.NpcEntity
import com.example.data.model.SfxPadEntity
import com.example.data.model.VoiceChronicleEntity
import com.example.data.model.WorldEntity

@Database(
    entities = [
        WorldEntity::class,
        LocationEntity::class,
        NpcEntity::class,
        VoiceChronicleEntity::class,
        SfxPadEntity::class
    ],
    version = 1,
    exportSchema = false
)
abstract class AppDatabase : RoomDatabase() {
    abstract fun bardoDao(): BardoDao

    companion object {
        @Volatile
        private var INSTANCE: AppDatabase? = null

        fun getInstance(context: Context): AppDatabase {
            return INSTANCE ?: synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    AppDatabase::class.java,
                    "o_bardo_database"
                )
                .fallbackToDestructiveMigration()
                .build()
                INSTANCE = instance
                instance
            }
        }
    }
}
