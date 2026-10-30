<?php

declare(strict_types=1);

namespace OBardo\Middleware;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\Campaign\CampaignRepository;

/**
 * Middleware para Permissão Administrativa Exclusiva do Mestre (GMOnly)
 */
final class GMMiddleware
{
    public function handle(Request $request, array $params = []): void
    {
        $user = $GLOBALS['auth_user'] ?? null;
        if (!$user) {
            Response::error('Não Autorizado', 'Usuário não autenticado.', 401);
        }

        $campaignId = (int)($params['id'] ?? $params['campaign_id'] ?? $request->input('campaign_id', 0));
        if ($campaignId <= 0) {
            Response::error('Parâmetro Inválido', 'ID da campanha não informado.', 400);
        }

        $campaignRepo = new CampaignRepository();
        if (!$campaignRepo->isGM($campaignId, $user->id)) {
            Response::error(
                'Acesso Negado',
                'Apenas o Mestre da campanha possui autorização para executar esta ação.',
                403,
                'https://obardo.com.br/errors/forbidden-gm-only'
            );
        }
    }
}
