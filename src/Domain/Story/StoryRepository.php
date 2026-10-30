<?php

declare(strict_types=1);

namespace OBardo\Domain\Story;

use OBardo\Core\Database;
use PDO;

/**
 * Repositório de Crônicas, Diário da Guilda, Handouts e Reviravoltas da Trama
 */
final class StoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getChronicles(int $campaignId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as author_name 
            FROM `chronicles` c
            INNER JOIN `users` u ON u.id = c.author_user_id
            WHERE c.campaign_id = :cid
            ORDER BY c.session_date DESC, c.id DESC
        ");
        $stmt->execute(['cid' => $campaignId]);

        return $stmt->fetchAll();
    }

    public function addChronicle(int $campaignId, int $authorUserId, string $title, string $content, ?string $sessionDate = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `chronicles` (`campaign_id`, `author_user_id`, `title`, `content_markdown`, `session_date`)
            VALUES (:cid, :uid, :title, :content, :date)
        ");
        $stmt->execute([
            'cid' => $campaignId,
            'uid' => $authorUserId,
            'title' => trim($title),
            'content' => trim($content),
            'date' => $sessionDate ?: date('Y-m-d'),
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getHandouts(int $campaignId, bool $onlyRevealed = true): array
    {
        $sql = "SELECT * FROM `handouts` WHERE `campaign_id` = :cid";
        if ($onlyRevealed) {
            $sql .= " AND `is_revealed` = 1";
        }
        $sql .= " ORDER BY `id` DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cid' => $campaignId]);

        return $stmt->fetchAll();
    }

    public function addHandout(int $campaignId, string $title, ?string $description = null, ?string $imageUrl = null, bool $isRevealed = false): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO `handouts` (`campaign_id`, `title`, `description`, `image_url`, `is_revealed`)
            VALUES (:cid, :title, :desc, :img, :rev)
        ");
        $stmt->execute([
            'cid' => $campaignId,
            'title' => trim($title),
            'desc' => $description,
            'img' => $imageUrl,
            'rev' => (int)$isRevealed,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function toggleHandoutVisibility(int $handoutId, bool $isRevealed): bool
    {
        $stmt = $this->db->prepare("UPDATE `handouts` SET `is_revealed` = :rev WHERE `id` = :id");
        return $stmt->execute(['rev' => (int)$isRevealed, 'id' => $handoutId]);
    }

    public function triggerPlotTwist(int $campaignId, string $headline, string $description, string $twistType = 'traicao', string $audioSfx = 'thunder'): array
    {
        $stmt = $this->db->prepare("
            INSERT INTO `plot_twists` (`campaign_id`, `twist_type`, `headline`, `description`, `audio_sfx`)
            VALUES (:cid, :type, :headline, :desc, :sfx)
        ");
        $stmt->execute([
            'cid' => $campaignId,
            'type' => $twistType,
            'headline' => trim($headline),
            'desc' => trim($description),
            'sfx' => $audioSfx,
        ]);

        return [
            'id' => (int)$this->db->lastInsertId(),
            'twist_type' => $twistType,
            'headline' => $headline,
            'description' => $description,
            'audio_sfx' => $audioSfx,
            'triggered_at' => date('Y-m-d H:i:s'),
        ];
    }
}
