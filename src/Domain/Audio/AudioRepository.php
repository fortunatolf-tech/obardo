<?php

declare(strict_types=1);

namespace OBardo\Domain\Audio;

use OBardo\Core\Database;
use PDO;

/**
 * Repositório de Trilha Sonora, Soundboard e Estado de Sincronia
 */
final class AudioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getTracks(?int $campaignId = null): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM `audio_tracks` 
            WHERE `is_preset` = 1 OR `campaign_id` = :cid 
            ORDER BY `category` ASC, `title` ASC
        ");
        $stmt->execute(['cid' => $campaignId]);

        return $stmt->fetchAll();
    }

    public function getSyncState(int $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, t.title as track_title, t.file_url as track_url, t.category as track_category 
            FROM `audio_sync_states` s
            LEFT JOIN `audio_tracks` t ON t.id = s.active_track_id
            WHERE s.campaign_id = :cid 
            LIMIT 1
        ");
        $stmt->execute(['cid' => $campaignId]);
        $row = $stmt->fetch();

        if (!$row) {
            return [
                'campaign_id' => $campaignId,
                'playback_state' => 'stopped',
                'active_track_id' => null,
                'server_timestamp' => (int)(microtime(true) * 1000),
                'start_offset_ms' => 0,
                'last_sfx_name' => null,
            ];
        }

        return $row;
    }

    public function updatePlayback(int $campaignId, string $action, ?int $trackId = null, string $layer = 'music', int $offsetMs = 0): array
    {
        $serverTimestamp = (int)(microtime(true) * 1000);
        $playbackState = match (strtoupper($action)) {
            'PLAY', 'PLAY_TRACK' => 'playing',
            'PAUSE', 'PAUSE_TRACK' => 'paused',
            default => 'stopped',
        };

        $stmt = $this->db->prepare("
            INSERT INTO `audio_sync_states` 
            (`campaign_id`, `active_track_id`, `active_layer`, `playback_state`, `server_timestamp`, `start_offset_ms`)
            VALUES (:cid, :tid, :layer, :state, :ts, :offset)
            ON DUPLICATE KEY UPDATE
            `active_track_id` = IFNULL(VALUES(`active_track_id`), `active_track_id`),
            `active_layer` = VALUES(`active_layer`),
            `playback_state` = VALUES(`playback_state`),
            `server_timestamp` = VALUES(`server_timestamp`),
            `start_offset_ms` = VALUES(`start_offset_ms`),
            `updated_at` = NOW()
        ");

        $stmt->execute([
            'cid' => $campaignId,
            'tid' => $trackId,
            'layer' => $layer,
            'state' => $playbackState,
            'ts' => $serverTimestamp,
            'offset' => $offsetMs,
        ]);

        return $this->getSyncState($campaignId);
    }

    public function triggerSFX(int $campaignId, string $sfxName): array
    {
        $serverTimestamp = (int)(microtime(true) * 1000);

        $stmt = $this->db->prepare("
            INSERT INTO `audio_sync_states` 
            (`campaign_id`, `last_sfx_name`, `last_sfx_timestamp`)
            VALUES (:cid, :sfx, :ts)
            ON DUPLICATE KEY UPDATE
            `last_sfx_name` = VALUES(`last_sfx_name`),
            `last_sfx_timestamp` = VALUES(`last_sfx_timestamp`),
            `updated_at` = NOW()
        ");

        $stmt->execute([
            'cid' => $campaignId,
            'sfx' => $sfxName,
            'ts' => $serverTimestamp,
        ]);

        return [
            'sfx' => $sfxName,
            'file_url' => $this->resolveSfxFile($sfxName),
            'timestamp' => $serverTimestamp,
        ];
    }

    /**
     * Catálogo completo de Efeitos Sonoros Instantâneos (SFX) Royalty-Free / CC0 (estilo Bardfy)
     */
    public function getSfxCatalog(): array
    {
        return [
            'combat' => [
                'name' => 'Combate & Armas',
                'items' => [
                    ['id' => 'espada_golpe', 'label' => 'Golpe de Espada', 'icon' => '⚔️', 'url' => '/audio/sfx/espada_golpe.ogg'],
                    ['id' => 'espada_choque', 'label' => 'Choque de Lâminas', 'icon' => '🗡️', 'url' => '/audio/sfx/espada_choque.ogg'],
                    ['id' => 'escudo_bloqueio', 'label' => 'Bloqueio Escudo', 'icon' => '🛡️', 'url' => '/audio/sfx/escudo_bloqueio.wav'],
                    ['id' => 'flecha_disparo', 'label' => 'Disparo de Flecha', 'icon' => '🏹', 'url' => '/audio/sfx/flecha_disparo.wav'],
                    ['id' => 'sacar_adaga', 'label' => 'Sacar Lâmina', 'icon' => '🗡️', 'url' => '/audio/sfx/sacar_adaga.ogg'],
                    ['id' => 'soco_impacto', 'label' => 'Soco / Impacto', 'icon' => '👊', 'url' => '/audio/sfx/soco_impacto.ogg'],
                ],
            ],
            'magic' => [
                'name' => 'Magia & Feitiços',
                'items' => [
                    ['id' => 'bola_de_fogo', 'label' => 'Bola de Fogo', 'icon' => '🔥', 'url' => '/audio/sfx/bola_de_fogo.ogg'],
                    ['id' => 'raio_trovao', 'label' => 'Raio Arcano', 'icon' => '⚡', 'url' => '/audio/sfx/raio_trovao.ogg'],
                    ['id' => 'cura_divina', 'label' => 'Cura Divina', 'icon' => '✨', 'url' => '/audio/sfx/cura_divina.ogg'],
                    ['id' => 'trovao_impacto', 'label' => 'Trovão Retumbante', 'icon' => '⛈️', 'url' => '/audio/sfx/trovao_impacto.wav'],
                    ['id' => 'pocao_beber', 'label' => 'Beber Poção', 'icon' => '🧪', 'url' => '/audio/sfx/pocao_beber.wav'],
                ],
            ],
            'monster' => [
                'name' => 'Monstros & Criaturas',
                'items' => [
                    ['id' => 'dragao_rugido', 'label' => 'Rugido de Dragão', 'icon' => '🐉', 'url' => '/audio/sfx/dragao_rugido.ogg'],
                    ['id' => 'lobo_uivo', 'label' => 'Uivo de Lobo', 'icon' => '🐺', 'url' => '/audio/sfx/lobo_uivo.ogg'],
                    ['id' => 'goblin_grito', 'label' => 'Emboscada Goblin', 'icon' => '👺', 'url' => '/audio/sfx/goblin_grito.ogg'],
                    ['id' => 'risada_maligna', 'label' => 'Risada Maligna', 'icon' => '😈', 'url' => '/audio/sfx/risada_maligna.ogg'],
                    ['id' => 'coracao_pulso', 'label' => 'Pulso de Pânico', 'icon' => '💓', 'url' => '/audio/sfx/coracao_pulso.ogg'],
                ],
            ],
            'tavern' => [
                'name' => 'Taverna & Social',
                'items' => [
                    ['id' => 'brinde_tacas', 'label' => 'Brinde de Canecas', 'icon' => '🍻', 'url' => '/audio/sfx/brinde_tacas.wav'],
                    ['id' => 'moedas_ouro', 'label' => 'Moedas de Ouro', 'icon' => '🪙', 'url' => '/audio/sfx/moedas_ouro.ogg'],
                    ['id' => 'dados_mesa', 'label' => 'Dados na Mesa', 'icon' => '🎲', 'url' => '/audio/sfx/dados_mesa.ogg'],
                    ['id' => 'sino_alarme', 'label' => 'Sino de Alarme', 'icon' => '🔔', 'url' => '/audio/sfx/sino_alarme.wav'],
                ],
            ],
            'dungeon' => [
                'name' => 'Masmorra & Exploração',
                'items' => [
                    ['id' => 'bau_tesouro', 'label' => 'Baú Abrindo', 'icon' => '🗄️', 'url' => '/audio/sfx/bau_tesouro.ogg'],
                    ['id' => 'porta_madeira', 'label' => 'Porta de Madeira', 'icon' => '🚪', 'url' => '/audio/sfx/porta_madeira.ogg'],
                    ['id' => 'porta_ferro', 'label' => 'Portão de Ferro', 'icon' => '⛓️', 'url' => '/audio/sfx/porta_ferro.wav'],
                    ['id' => 'rangido_madeira', 'label' => 'Rangido Sinistro', 'icon' => '🏚️', 'url' => '/audio/sfx/rangido_madeira.ogg'],
                    ['id' => 'passos_pedra', 'label' => 'Passos na Cripta', 'icon' => '👣', 'url' => '/audio/sfx/passos_pedra.ogg'],
                ],
            ],
        ];
    }

    /**
     * Resolve o arquivo de áudio para o identificador SFX
     */
    public function resolveSfxFile(string $sfxName): string
    {
        $map = [
            'sword' => '/audio/sfx/espada_golpe.ogg',
            'espada_golpe' => '/audio/sfx/espada_golpe.ogg',
            'espada_choque' => '/audio/sfx/espada_choque.ogg',
            'escudo_bloqueio' => '/audio/sfx/escudo_bloqueio.wav',
            'flecha_disparo' => '/audio/sfx/flecha_disparo.wav',
            'sacar_adaga' => '/audio/sfx/sacar_adaga.ogg',
            'soco_impacto' => '/audio/sfx/soco_impacto.ogg',
            'corte_lamina' => '/audio/sfx/corte_lamina.ogg',
            'spell' => '/audio/sfx/raio_trovao.ogg',
            'raio_trovao' => '/audio/sfx/raio_trovao.ogg',
            'bola_de_fogo' => '/audio/sfx/bola_de_fogo.ogg',
            'cura_divina' => '/audio/sfx/cura_divina.ogg',
            'trovao_impacto' => '/audio/sfx/trovao_impacto.wav',
            'thunder' => '/audio/sfx/trovao_impacto.wav',
            'pocao_beber' => '/audio/sfx/pocao_beber.wav',
            'roar' => '/audio/sfx/dragao_rugido.ogg',
            'dragao_rugido' => '/audio/sfx/dragao_rugido.ogg',
            'lobo_uivo' => '/audio/sfx/lobo_uivo.ogg',
            'goblin_grito' => '/audio/sfx/goblin_grito.ogg',
            'risada_maligna' => '/audio/sfx/risada_maligna.ogg',
            'coracao_pulso' => '/audio/sfx/coracao_pulso.ogg',
            'brinde_tacas' => '/audio/sfx/brinde_tacas.wav',
            'cheers' => '/audio/sfx/brinde_tacas.wav',
            'moedas_ouro' => '/audio/sfx/moedas_ouro.ogg',
            'coins' => '/audio/sfx/moedas_ouro.ogg',
            'dados_mesa' => '/audio/sfx/dados_mesa.ogg',
            'dice' => '/audio/sfx/dados_mesa.ogg',
            'crit' => '/audio/sfx/brinde_tacas.wav',
            'sino_alarme' => '/audio/sfx/sino_alarme.wav',
            'bau_tesouro' => '/audio/sfx/bau_tesouro.ogg',
            'porta_madeira' => '/audio/sfx/porta_madeira.ogg',
            'porta_fechando' => '/audio/sfx/porta_fechando.ogg',
            'porta_ferro' => '/audio/sfx/porta_ferro.wav',
            'rangido_madeira' => '/audio/sfx/rangido_madeira.ogg',
            'passos_pedra' => '/audio/sfx/passos_pedra.ogg',
        ];

        return $map[$sfxName] ?? '/audio/sfx/dados_mesa.ogg';
    }
}
