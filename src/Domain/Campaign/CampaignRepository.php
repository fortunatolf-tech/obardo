<?php

declare(strict_types=1);

namespace OBardo\Domain\Campaign;

use OBardo\Core\Database;
use PDO;

/**
 * Repositório de Campanhas e Membros
 */
final class CampaignRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?Campaign
    {
        $stmt = $this->db->prepare("SELECT * FROM `campaigns` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByCode(string $code): ?Campaign
    {
        $stmt = $this->db->prepare("SELECT * FROM `campaigns` WHERE `code` = :code LIMIT 1");
        $stmt->execute(['code' => strtoupper(trim($code))]);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, m.member_role 
            FROM `campaigns` c
            INNER JOIN `campaign_members` m ON m.campaign_id = c.id
            WHERE m.user_id = :user_id AND c.is_active = 1
            ORDER BY c.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);

        $campaigns = [];
        while ($row = $stmt->fetch()) {
            $campaign = $this->hydrate($row)->toArray();
            $campaign['user_role'] = $row['member_role'];
            $campaigns[] = $campaign;
        }

        return $campaigns;
    }

    public function create(
        int $gmUserId,
        string $title,
        string $ruleSystem = 'TORMENTA20',
        ?string $description = null,
        ?string $coverUrl = null
    ): Campaign {
        // Gera código alfanumérico curto único
        $code = 'BARDO-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $stmt = $this->db->prepare("
            INSERT INTO `campaigns` (`gm_user_id`, `title`, `code`, `rule_system`, `description`, `cover_url`)
            VALUES (:gm, :title, :code, :system, :desc, :cover)
        ");

        $stmt->execute([
            'gm' => $gmUserId,
            'title' => trim($title),
            'code' => $code,
            'system' => $ruleSystem,
            'desc' => $description,
            'cover' => $coverUrl ?: '/assets/covers/default.webp',
        ]);

        $campaignId = (int)$this->db->lastInsertId();

        // Adiciona o criador como Mestre (GM)
        $this->addMember($campaignId, $gmUserId, 'gm');

        // Inicializa estado de áudio da campanha
        $stmtAudio = $this->db->prepare("
            INSERT INTO `audio_sync_states` (`campaign_id`, `active_track_id`, `playback_state`)
            VALUES (:cid, 1, 'stopped')
            ON DUPLICATE KEY UPDATE `updated_at` = NOW()
        ");
        $stmtAudio->execute(['cid' => $campaignId]);

        return new Campaign($campaignId, $gmUserId, $title, $code, $ruleSystem, $description, $coverUrl);
    }

    public function addMember(int $campaignId, int $userId, string $role = 'player'): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO `campaign_members` (`campaign_id`, `user_id`, `member_role`)
            VALUES (:cid, :uid, :role)
            ON DUPLICATE KEY UPDATE `member_role` = VALUES(`member_role`)
        ");

        return $stmt->execute([
            'cid' => $campaignId,
            'uid' => $userId,
            'role' => $role,
        ]);
    }

    public function getMembers(int $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, m.member_role, m.joined_at
            FROM `campaign_members` m
            INNER JOIN `users` u ON u.id = m.user_id
            WHERE m.campaign_id = :cid
            ORDER BY FIELD(m.member_role, 'gm', 'co_gm', 'player', 'observer'), u.name ASC
        ");
        $stmt->execute(['cid' => $campaignId]);

        return $stmt->fetchAll();
    }

    public function isGM(int $campaignId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1 FROM `campaign_members` 
            WHERE `campaign_id` = :cid AND `user_id` = :uid AND `member_role` IN ('gm', 'co_gm') 
            LIMIT 1
        ");
        $stmt->execute(['cid' => $campaignId, 'uid' => $userId]);

        return (bool)$stmt->fetchColumn();
    }

    public function isMember(int $campaignId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1 FROM `campaign_members` 
            WHERE `campaign_id` = :cid AND `user_id` = :uid 
            LIMIT 1
        ");
        $stmt->execute(['cid' => $campaignId, 'uid' => $userId]);

        return (bool)$stmt->fetchColumn();
    }

    public function updateGMSecrets(int $campaignId, string $secrets): bool
    {
        $stmt = $this->db->prepare("UPDATE `campaigns` SET `gm_secrets` = :sec WHERE `id` = :id");
        return $stmt->execute([
            'sec' => $secrets,
            'id' => $campaignId,
        ]);
    }

    public function updateMemberRole(int $campaignId, int $userId, string $role): bool
    {
        $validRoles = ['gm', 'co_gm', 'player', 'observer'];
        if (!in_array($role, $validRoles, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE `campaign_members` 
            SET `member_role` = :role 
            WHERE `campaign_id` = :cid AND `user_id` = :uid
        ");
        return $stmt->execute([
            'role' => $role,
            'cid' => $campaignId,
            'uid' => $userId,
        ]);
    }

    public function removeMember(int $campaignId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `campaign_members` WHERE `campaign_id` = :cid AND `user_id` = :uid");
        return $stmt->execute([
            'cid' => $campaignId,
            'uid' => $userId,
        ]);
    }

    private function hydrate(array $row): Campaign
    {
        return new Campaign(
            (int)$row['id'],
            (int)$row['gm_user_id'],
            $row['title'],
            $row['code'],
            $row['rule_system'],
            $row['description'],
            $row['gm_secrets'] ?? null,
            $row['cover_url'],
            (bool)$row['is_active'],
            $row['created_at']
        );
    }
}
