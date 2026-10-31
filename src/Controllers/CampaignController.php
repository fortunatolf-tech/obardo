<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Campaign\CampaignRepository;

/**
 * Controlador de Gestão de Campanhas e Membros
 */
final class CampaignController
{
    private CampaignRepository $campaignRepo;

    public function __construct()
    {
        $this->campaignRepo = new CampaignRepository();
    }

    public function index(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $campaigns = $this->campaignRepo->findByUser($user->id);

        Response::json([
            'status' => 'success',
            'data' => [
                'campaigns' => $campaigns,
            ]
        ]);
    }

    public function store(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $title = trim((string)$request->input('title', ''));
        $ruleSystem = (string)$request->input('rule_system', 'TORMENTA20');
        $description = (string)$request->input('description', '');
        $coverUrl = (string)$request->input('cover_url', '');

        if ($title === '') {
            Response::error('Validação Inválida', 'O título da campanha é obrigatório.', 422);
        }

        $campaign = $this->campaignRepo->create($user->id, $title, $ruleSystem, $description, $coverUrl);

        if (str_starts_with($request->path, '/api/')) {
            Response::json([
                'status' => 'success',
                'message' => 'Campanha criada com sucesso! Você é o Mestre desta mesa.',
                'data' => [
                    'campaign' => $campaign->toArray(),
                ]
            ], 201);
        } else {
            Response::redirect('/campaigns/' . $campaign->id);
        }
    }

    public function show(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $campaign = $this->campaignRepo->findById($campaignId);

        if (!$campaign) {
            Response::error('Campanha Não Encontrada', 'A campanha solicitada não existe.', 404);
        }

        $members = $this->campaignRepo->getMembers($campaignId);

        Response::json([
            'status' => 'success',
            'data' => [
                'campaign' => $campaign->toArray(),
                'members' => $members,
            ]
        ]);
    }

    public function join(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $code = strtoupper(trim((string)$request->input('code', '')));

        if ($code === '') {
            Response::error('Validação Inválida', 'Código de convite é obrigatório.', 422);
        }

        $campaign = $this->campaignRepo->findByCode($code);
        if (!$campaign) {
            Response::error('Convite Inválido', 'Nenhuma campanha encontrada com este código.', 404);
        }

        $this->campaignRepo->addMember($campaign->id, $user->id, 'player');

        if (str_starts_with($request->path, '/api/')) {
            Response::json([
                'status' => 'success',
                'message' => "Você ingressou com sucesso na campanha '{$campaign->title}'!",
                'data' => [
                    'campaign' => $campaign->toArray(),
                ]
            ]);
        } else {
            Response::redirect('/campaigns/' . $campaign->id . '/sheet');
        }
    }

    public function members(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $members = $this->campaignRepo->getMembers($campaignId);

        Response::json([
            'status' => 'success',
            'data' => [
                'members' => $members,
            ]
        ]);
    }

    public function updateSecrets(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $secrets = (string)$request->input('gm_secrets', '');

        $this->campaignRepo->updateGMSecrets($campaignId, $secrets);

        Response::json([
            'status' => 'success',
            'message' => 'Segredos do Mestre salvos com isolamento criptográfico garantido (RF17/RN06).',
            'data' => [
                'campaign_id' => $campaignId,
            ]
        ]);
    }

    public function updateMemberRole(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $targetUserId = (int)$params['user_id'];
        $role = (string)$request->input('role', 'player');

        $this->campaignRepo->updateMemberRole($campaignId, $targetUserId, $role);

        Response::json([
            'status' => 'success',
            'message' => 'Cargo do participante atualizado com sucesso.',
            'data' => [
                'user_id' => $targetUserId,
                'role' => $role,
            ]
        ]);
    }

    public function kickMember(Request $request, array $params): void
    {
        $campaignId = (int)$params['id'];
        $targetUserId = (int)$params['user_id'];

        $this->campaignRepo->removeMember($campaignId, $targetUserId);

        Response::json([
            'status' => 'success',
            'message' => 'Participante removido da campanha com sucesso.',
        ]);
    }
}
