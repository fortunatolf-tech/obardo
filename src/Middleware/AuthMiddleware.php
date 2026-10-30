<?php

declare(strict_types=1);

namespace OBardo\Middleware;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\User\UserRepository;

/**
 * Middleware de Autenticação via Token Bearer ou Cookie de Sessão
 */
final class AuthMiddleware
{
    public function handle(Request $request, array $params = []): void
    {
        $token = $request->getBearerToken();
        if (!$token) {
            $this->unauthorized($request);
        }

        $userRepo = new UserRepository();
        $user = $userRepo->findByToken($token);

        if (!$user) {
            $this->unauthorized($request);
        }

        // Armazena usuário autenticado globalmente na requisição
        $GLOBALS['auth_user'] = $user;
    }

    private function unauthorized(Request $request): void
    {
        if (str_starts_with($request->path, '/api/')) {
            Response::error(
                'Não Autorizado',
                'Token de autenticação ausente ou inválido.',
                401,
                'https://obardo.com.br/errors/unauthorized'
            );
        } else {
            Response::redirect('/login?redirect=' . urlencode($request->uri));
        }
    }
}
