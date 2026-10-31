<?php

declare(strict_types=1);

namespace OBardo\Controllers;

use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Domain\User\UserRepository;

/**
 * Controlador de Autenticação e Usuários
 */
final class AuthController
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register(Request $request): void
    {
        $name = trim((string)$request->input('name', ''));
        $email = trim((string)$request->input('email', ''));
        $password = (string)$request->input('password', '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Validação Inválida', 'Nome e e-mail válido são obrigatórios.', 422);
        }

        if (strlen($password) < 8) {
            Response::error('Senha Muito Fraca', 'A senha deve conter no mínimo 8 caracteres.', 422);
        }

        if ($this->userRepo->findByEmail($email)) {
            Response::error('Conflito de Conta', 'Este endereço de e-mail já está cadastrado.', 409);
        }

        $user = $this->userRepo->create($name, $email, $password);

        // Define cookie seguro de sessão
        setcookie('bardo_token', $user->token, [
            'expires' => time() + 86400 * 30,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (str_starts_with($request->path, '/api/')) {
            Response::json([
                'status' => 'success',
                'message' => 'Conta criada com sucesso!',
                'data' => [
                    'user' => $user->toArray(),
                    'token' => $user->token,
                ]
            ], 201);
        } else {
            Response::redirect('/dashboard');
        }
    }

    public function login(Request $request): void
    {
        $email = trim((string)$request->input('email', ''));
        $password = (string)$request->input('password', '');

        $user = $this->userRepo->findByEmail($email);
        if (!$user || !password_verify($password, $user->passwordHash)) {
            Response::error('Credenciais Inválidas', 'E-mail ou senha incorretos.', 401);
        }

        $token = $this->userRepo->updateToken($user->id);

        setcookie('bardo_token', $token, [
            'expires' => time() + 86400 * 30,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (str_starts_with($request->path, '/api/')) {
            Response::json([
                'status' => 'success',
                'message' => 'Autenticado com sucesso!',
                'data' => [
                    'user' => $user->toArray(),
                    'token' => $token,
                ]
            ]);
        } else {
            Response::redirect('/dashboard');
        }
    }

    public function me(Request $request): void
    {
        $user = $GLOBALS['auth_user'] ?? null;
        if (!$user) {
            Response::error('Não Autorizado', 'Usuário não autenticado.', 401);
        }

        Response::json([
            'status' => 'success',
            'data' => [
                'user' => $user->toArray(),
            ]
        ]);
    }

    public function logout(Request $request): void
    {
        setcookie('bardo_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (str_starts_with($request->path, '/api/')) {
            Response::json(['status' => 'success', 'message' => 'Sessão encerrada com sucesso.']);
        } else {
            Response::redirect('/login');
        }
    }

    public function exportData(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $data = $this->userRepo->exportUserData($user->id);

        Response::json([
            'status' => 'success',
            'message' => 'Relatório oficial de dados gerado conforme Art. 18 da LGPD.',
            'data' => $data,
        ]);
    }

    public function deleteAccount(Request $request): void
    {
        $user = $GLOBALS['auth_user'];
        $this->userRepo->delete($user->id);

        setcookie('bardo_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        Response::json([
            'status' => 'success',
            'message' => 'Conta e dados pessoais excluídos definitivamente em conformidade com a LGPD.',
        ]);
    }
}
