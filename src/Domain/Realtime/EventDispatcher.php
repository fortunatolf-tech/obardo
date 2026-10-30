<?php

declare(strict_types=1);

namespace OBardo\Domain\Realtime;

use OBardo\Core\Database;
use PDO;

/**
 * Despachante de Eventos em Tempo Real para o Canal SSE
 */
final class EventDispatcher
{
    public static function dispatch(int $campaignId, string $eventType, array $payload): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO `campaign_events` (`campaign_id`, `event_type`, `payload`, `created_at_micro`)
            VALUES (:cid, :type, :payload, :micro)
        ");

        $stmt->execute([
            'cid' => $campaignId,
            'type' => $eventType,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'micro' => microtime(true),
        ]);
    }

    public static function getEventsAfter(int $campaignId, int $lastEventId, int $limit = 20): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM `campaign_events` 
            WHERE `campaign_id` = :cid AND `id` > :last_id 
            ORDER BY `id` ASC 
            LIMIT :lim
        ");
        $stmt->bindValue(':cid', $campaignId, PDO::PARAM_INT);
        $stmt->bindValue(':last_id', $lastEventId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $events = $stmt->fetchAll();
        foreach ($events as &$e) {
            $e['payload'] = json_decode($e['payload'], true);
        }

        return $events;
    }
}
