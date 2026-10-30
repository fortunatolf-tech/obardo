<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Realtime\EventDispatcher;

/**
 * Controlador de Streaming em Tempo Real via Server-Sent Events (SSE)
 */
final class SSEStreamController
{
    public function stream(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $lastEventId = (int)($request->headers['last-event-id'] ?? $request->input('last_event_id', 0));

        Response::sseHeaders();

        // Handshake inicial
        $this->sendSSE('connected', [
            'message' => 'Sintonizado com o canal acústico e narrativo d\'O Bardo',
            'campaign_id' => $campaignId,
            'server_time' => (int)(microtime(true) * 1000),
        ], $lastEventId);

        // Ciclo de escuta contínua de eventos
        $cycles = 0;
        $maxCycles = 120; // 60 segundos por ciclo de conexão (reconexão automática transparente pelo browser)

        while ($cycles < $maxCycles) {
            if (connection_aborted()) {
                break;
            }

            $events = EventDispatcher::getEventsAfter($campaignId, $lastEventId, 10);
            if (!empty($events)) {
                foreach ($events as $evt) {
                    $lastEventId = (int)$evt['id'];
                    $this->sendSSE($evt['event_type'], $evt['payload'], $lastEventId);
                }
            } else {
                // Heartbeat para manter conexão viva
                if ($cycles % 20 === 0) {
                    echo ": heartbeat\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                }
            }

            $cycles++;
            usleep(500000); // 500ms
        }
        exit;
    }

    private function sendSSE(string $event, array $data, int $id = 0): void
    {
        if ($id > 0) {
            echo "id: {$id}\n";
        }
        echo "event: {$event}\n";
        echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";

        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
}
