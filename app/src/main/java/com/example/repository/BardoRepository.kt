package com.example.repository

import com.example.data.local.BardoDao
import com.example.data.model.LocationEntity
import com.example.data.model.NpcEntity
import com.example.data.model.SfxPadEntity
import com.example.data.model.VoiceChronicleEntity
import com.example.data.model.WorldEntity
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.withContext

class BardoRepository(private val dao: BardoDao) {

    val allWorlds: Flow<List<WorldEntity>> = dao.getAllWorlds()
    val allChronicles: Flow<List<VoiceChronicleEntity>> = dao.getAllChronicles()
    val allSfxPads: Flow<List<SfxPadEntity>> = dao.getAllSfxPads()
    val currentSceneLocation: Flow<LocationEntity?> = dao.getCurrentSceneLocation()

    fun getLocationsForWorld(worldId: Long): Flow<List<LocationEntity>> =
        dao.getLocationsForWorld(worldId)

    fun getNpcsForLocation(locationId: Long): Flow<List<NpcEntity>> =
        dao.getNpcsForLocation(locationId)

    fun getChroniclesForLocation(locationId: Long): Flow<List<VoiceChronicleEntity>> =
        dao.getChroniclesForLocation(locationId)

    suspend fun selectSceneLocation(locationId: Long) = withContext(Dispatchers.IO) {
        dao.setCurrentScene(locationId)
    }

    suspend fun addWorld(name: String, system: String, description: String): Long = withContext(Dispatchers.IO) {
        dao.insertWorld(WorldEntity(name = name, system = system, description = description))
    }

    suspend fun addLocation(location: LocationEntity): Long = withContext(Dispatchers.IO) {
        dao.insertLocation(location)
    }

    suspend fun updateLocation(location: LocationEntity) = withContext(Dispatchers.IO) {
        dao.updateLocation(location)
    }

    suspend fun addNpc(npc: NpcEntity): Long = withContext(Dispatchers.IO) {
        dao.insertNpc(npc)
    }

    suspend fun deleteNpc(npcId: Long) = withContext(Dispatchers.IO) {
        dao.deleteNpc(npcId)
    }

    suspend fun addVoiceChronicle(chronicle: VoiceChronicleEntity): Long = withContext(Dispatchers.IO) {
        dao.insertChronicle(chronicle)
    }

    suspend fun seedDefaultDataIfEmpty() = withContext(Dispatchers.IO) {
        // Seed default SFX pads
        val defaultPads = listOf(
            SfxPadEntity("sword", "Lâmina", "1", "SWORD", "Hardware"),
            SfxPadEntity("thunder", "Trovão", "2", "THUNDER", "FlashOn"),
            SfxPadEntity("magic", "Magia Arcana", "3", "MAGIC", "AutoAwesome"),
            SfxPadEntity("door", "Porta Rangendo", "4", "DOOR", "DoorBack"),
            SfxPadEntity("roar", "Rugido de Monstro", "5", "ROAR", "Warning"),
            SfxPadEntity("dice", "Rolagem de Dados", "6", "DICE", "Casino")
        )
        dao.insertSfxPads(defaultPads)

        // Check if worlds exist
        // Note:We seed if first run
        val world1Id = dao.insertWorld(
            WorldEntity(
                name = "A Maldição de Barovia",
                system = "D&D 5e",
                description = "Um reino sombrio preso nas brumas governado pelo conde vampiro Strahd von Zarovich."
            )
        )

        val world2Id = dao.insertWorld(
            WorldEntity(
                name = "Tormenta20 - Arton",
                system = "Tormenta20",
                description = "O mundo de Arton sob a ameaça da tempestade rubra da Tormenta."
            )
        )

        // Seed Locations for Barovia
        val loc1Id = dao.insertLocation(
            LocationEntity(
                worldId = world1Id,
                name = "Castelo Ravenloft - Salão das Bruxas",
                parentLocation = "Castelo Ravenloft",
                description = "Teto abobadado de pedra com teias de aranha gigantes. O som da tempestade lá fora retumba nas vidraças góticas quebradas.",
                secrets = "Atrás do tapete da parede ocidental existe uma passagem secreta para as masmorras inferiores.",
                ambientSoundKey = "RAIN",
                musicTrackKey = "MYSTERY",
                imageUrl = "https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=1000&q=80",
                isCurrentScene = true
            )
        )

        val loc2Id = dao.insertLocation(
            LocationEntity(
                worldId = world1Id,
                name = "Taverna do Sangue de Vinho",
                parentLocation = "Vila de Barovia",
                description = "Cheiro pesado de cerveja azeda e lenha queimada. Os aldeões conversam em sussurros amedrontados nos cantos escuros.",
                secrets = "O taverneiro Arik guarda um bilhete com o selo secreto da família Zarovich sob o balcão.",
                ambientSoundKey = "TAVERN",
                musicTrackKey = "EXPLORATION",
                imageUrl = "https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=1000&q=80",
                isCurrentScene = false
            )
        )

        val loc3Id = dao.insertLocation(
            LocationEntity(
                worldId = world1Id,
                name = "Floresta das Brumas",
                parentLocation = "Caminhos de Barovia",
                description = "Névoa espessa e esbranquiçada reduz a visibilidade a poucos passos. Uivos de lobos ecoam na distância.",
                secrets = "Um santuário profano coberto de musgo concede proteção contra metamorfos.",
                ambientSoundKey = "WIND",
                musicTrackKey = "INVESTIGATION",
                imageUrl = "https://images.unsplash.com/photo-1448375240586-882707db888b?w=1000&q=80",
                isCurrentScene = false
            )
        )

        // Seed Locations for Tormenta20
        dao.insertLocation(
            LocationEntity(
                worldId = world2Id,
                name = "Valkaria - Distrito do Mercado",
                parentLocation = "Reino de Deheon",
                description = "Mercadores de todas as raças vendem especiarias, pergaminhos e artefatos mágicos sob a sombra da Estátua da Deusa.",
                secrets = "O mercador osteon vende poções ilegais de Mana Vermelha escondidas no fundo de um tonel.",
                ambientSoundKey = "TAVERN",
                musicTrackKey = "EXPLORATION",
                imageUrl = "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1000&q=80",
                isCurrentScene = false
            )
        )

        // Seed NPCs for Loc1
        dao.insertNpc(
            NpcEntity(
                locationId = loc1Id,
                name = "Strahd von Zarovich",
                role = "Vilão Principal / Senhor do Castelo",
                notes = "Frio, elegante e extremamente estrategista. Fala com voz grave e pausada.",
                quote = "Bem-vindos ao meu domínio... vocês serão meus convidados para o jantar.",
                hp = 144,
                ac = 16
            )
        )

        dao.insertNpc(
            NpcEntity(
                locationId = loc1Id,
                name = "Bruxa Morgantha",
                role = "Antagonista Secundário",
                notes = "Vende tortas dos sonhos feitas com poeira de ossos. Possui garras afiadas.",
                quote = "Querem uma tortinha, meus jovens aventureiros?",
                hp = 112,
                ac = 17
            )
        )

        // Seed NPCs for Loc2
        dao.insertNpc(
            NpcEntity(
                locationId = loc2Id,
                name = "Ismark o Cobras",
                role = "Aliado / Burgomestre de Barovia",
                notes = "Desesperado para proteger sua irmã Ireena dos ataques noturnos de Strahd.",
                quote = "Ajudem minha irmã a escapar para Vallaki antes que seja tarde!",
                hp = 58,
                ac = 15
            )
        )

        // Seed Initial Voice Chronicles
        dao.insertChronicle(
            VoiceChronicleEntity(
                locationId = loc1Id,
                rawText = "O grupo encontrou o selo da bruxa no salão e derrotou 3 esqueletos gárgula.",
                formattedLore = "📜 **Crônica da Sessão**:\n• O paladino destruiu o selo profano de Morgantha com Golpe Divino.\n• O bruxo decifrou os glifos no altar de pedra.\n• Consequência: Strahd agora sabe da presença do grupo no salão."
            )
        )
    }
}
