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
            'timestamp' => $serverTimestamp,
        ];
    }
}
