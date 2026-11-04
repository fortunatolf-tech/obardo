<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Audio\AudioRepository;
use OBardo\Domain\Realtime\EventDispatcher;

/**
 * Controlador de Som e Ambientação Acústica Sincronizada
 */
final class AudioController
{
    private AudioRepository $audioRepo;

    public function __construct()
    {
        $this->audioRepo = new AudioRepository();
    }

    public function library(Request $request, array $params): void
    {
        $campaignId = isset($params['id']) ? (int)$params['id'] : null;
        $tracks = $this->audioRepo->getTracks($campaignId);
        $sfxCatalog = $this->audioRepo->getSfxCatalog();

        Response::json([
            'status' => 'success',
            'data' => [
                'tracks' => $tracks,
                'sfx_catalog' => $sfxCatalog,
                'sfx_presets' => ['sword', 'thunder', 'spell', 'roar', 'dice', 'crit'],
            ]
        ]);
    }

    public function liveState(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $state = $this->audioRepo->getSyncState($campaignId);

        Response::json([
            'status' => 'success',
            'data' => [
                'sync_state' => $state,
                'current_server_time' => (int)(microtime(true) * 1000),
            ]
        ]);
    }

    public function sync(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $action = strtoupper((string)$request->input('action', 'PLAY'));
        $trackId = $request->input('track_id') ? (int)$request->input('track_id') : null;
        $layer = (string)$request->input('layer', 'music');
        $offsetMs = (int)$request->input('start_offset_ms', 0);
        $sfxName = (string)$request->input('sfx_name', '');

        if ($action === 'TRIGGER_SFX' && $sfxName !== '') {
            $sfxState = $this->audioRepo->triggerSFX($campaignId, $sfxName);

            // Transmissão imediata via SSE
            EventDispatcher::dispatch($campaignId, 'audio_sfx_command', [
                'sfx_name' => $sfxName,
                'file_url' => $sfxState['file_url'] ?? '',
                'server_timestamp' => $sfxState['timestamp'],
            ]);

            Response::json([
                'status' => 'success',
                'message' => "Efeito sonoro '{$sfxName}' disparado na sessão.",
                'data' => $sfxState,
            ]);
            return;
        }

        $newState = $this->audioRepo->updatePlayback($campaignId, $action, $trackId, $layer, $offsetMs);

        // Transmite comando de reprodução sincronizada para todos os membros
        EventDispatcher::dispatch($campaignId, 'audio_sync_command', [
            'action' => $action,
            'track_id' => $trackId,
            'layer' => $layer,
            'start_offset_ms' => $offsetMs,
            'server_timestamp' => $newState['server_timestamp'],
            'track_title' => $newState['track_title'] ?? '',
            'track_url' => $newState['track_url'] ?? '',
        ]);

        Response::json([
            'status' => 'success',
            'message' => "Comando acústico '{$action}' sincronizado com sucesso.",
            'data' => [
                'sync_state' => $newState,
            ]
        ]);
    }
}
