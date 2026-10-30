<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Realtime\EventDispatcher;
use OBardo\Domain\Story\StoryRepository;

/**
 * Controlador de Crônicas, Diário da Guilda, Handouts e Reviravoltas da Trama
 */
final class StoryController
{
    private StoryRepository $storyRepo;

    public function __construct()
    {
        $this->storyRepo = new StoryRepository();
    }

    public function chronicles(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $chronicles = $this->storyRepo->getChronicles($campaignId);

        Response::json([
            'status' => 'success',
            'data' => [
                'chronicles' => $chronicles,
            ]
        ]);
    }

    public function storeChronicle(Request $request, array $params): void
    {
        $user = $GLOBALS['auth_user'];
        $campaignId = (int)$params['id'];
        $title = trim((string)$request->input('title', ''));
        $content = trim((string)$request->input('content', ''));
        $sessionDate = (string)$request->input('session_date', date('Y-m-d'));

        if ($title === '' || $content === '') {
            Response::error('Validação Inválida', 'Título e conteúdo da crônica são obrigatórios.', 422);
        }

        $id = $this->storyRepo->addChronicle($campaignId, $user->id, $title, $content, $sessionDate);

        EventDispatcher::dispatch($campaignId, 'chronicle_added', [
            'id' => $id,
            'title' => $title,
            'author_name' => $user->name,
            'session_date' => $sessionDate,
        ]);

        Response::json([
            'status' => 'success',
            'message' => 'Crônica adicionada com sucesso ao diário da guilda.',
            'data' => [
                'id' => $id,
            ]
        ], 201);
    }

    public function handouts(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $handouts = $this->storyRepo->getHandouts($campaignId, false);

        Response::json([
            'status' => 'success',
            'data' => [
                'handouts' => $handouts,
            ]
        ]);
    }

    public function triggerPlotTwist(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $headline = trim((string)$request->input('headline', ''));
        $description = trim((string)$request->input('description', ''));
        $twistType = (string)$request->input('twist_type', 'traicao');
        $audioSfx = (string)$request->input('audio_sfx', 'thunder');

        if ($headline === '' || $description === '') {
            Response::error('Validação Inválida', 'Manchete e descrição da reviravolta são obrigatórias.', 422);
        }

        $twist = $this->storyRepo->triggerPlotTwist($campaignId, $headline, $description, $twistType, $audioSfx);

        // Dispara evento dramático para todos os celulares e computadores na sessão!
        EventDispatcher::dispatch($campaignId, 'plot_twist_broadcast', $twist);

        Response::json([
            'status' => 'success',
            'message' => 'Reviravolta da trama ativada com sucesso na tela de todos os participantes!',
            'data' => [
                'plot_twist' => $twist,
            ]
        ]);
    }
}
