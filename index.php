<?php

declare(strict_types=1);

/**
 * O BARDO • Plataforma Web para RPG de Mesa Tradicional (Tormenta20 & D&D)
 * Front Controller e Ponto de Entrada da Aplicação
 * PHP 8.3 Native Architecture
 */

// Tratamento de cabeçalhos CORS e cabeçalhos de segurança básicos
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, Last-Event-ID, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Inicialização do Composer Autoloader
require_once __DIR__ . '/vendor/autoload.php';

use OBardo\Core\Config;
use OBardo\Core\Request;
use OBardo\Core\Response;
use OBardo\Core\Router;
use OBardo\Middleware\AuthMiddleware;
use OBardo\Middleware\GMMiddleware;
use OBardo\Controllers\AuthController;
use OBardo\Controllers\CampaignController;
use OBardo\Controllers\CharacterController;
use OBardo\Controllers\AudioController;
use OBardo\Controllers\StoryController;
use OBardo\Controllers\SSEStreamController;
use OBardo\Controllers\WebViewController;

// Carregamento de variáveis de ambiente
Config::load(__DIR__ . '/.env');

// Configuração de ambiente e tratamento de exceções
$appEnv = Config::get('APP_ENV', 'production');
if ($appEnv === 'development' || Config::get('APP_DEBUG', 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

set_exception_handler(static function (Throwable $e) {
    error_log("[O Bardo Fatal Error] " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    $isApi = str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    if ($isApi) {
        Response::error(
            'Erro Interno do Servidor',
            Config::get('APP_DEBUG', 'false') === 'true' ? $e->getMessage() : 'Ocorreu um erro inesperado no servidor do Bardo.',
            500
        );
    } else {
        http_response_code(500);
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Erro no Servidor • O Bardo</title></head>";
        echo "<body style='background:#0d0f17;color:#f3f4f6;font-family:sans-serif;padding:2rem;text-align:center;'>";
        echo "<h1 style='color:#e11d48;'>⚔️ 500 - Desafio dos Deuses</h1>";
        echo "<p>Houve uma falha inesperada na taverna. Nossos bardos já foram notificados.</p>";
        echo "<a href='/' style='color:#d4af37;text-decoration:none;font-weight:bold;'>Retornar à Taverna</a>";
        echo "</body></html>";
        exit;
    }
});

// Instanciação da Requisição e Roteador
$request = new Request();
$router = new Router();

// =============================================================================
// ROTAS DE INTERFACE WEB (SSR + INTERATIVIDADE MOBILE CLIENT-SIDE)
// =============================================================================

// Páginas Públicas & Autenticação
$router->get('/', [WebViewController::class, 'home']);
$router->get('/login', [WebViewController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [WebViewController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Painel do Jogador & Campanhas
$router->get('/dashboard', [WebViewController::class, 'dashboard'], [AuthMiddleware::class]);
$router->post('/campaigns', [CampaignController::class, 'store'], [AuthMiddleware::class]);
$router->post('/join', [CampaignController::class, 'join'], [AuthMiddleware::class]);

// Mesas e Salas de Jogo
$router->get('/campaigns/{id}', [WebViewController::class, 'liveTaverna'], [AuthMiddleware::class]);
$router->get('/campaigns/{id}/sheet', [WebViewController::class, 'playerSheet'], [AuthMiddleware::class]);
$router->get('/campaigns/{id}/gm', [WebViewController::class, 'gmDashboard'], [AuthMiddleware::class, GMMiddleware::class]);
$router->get('/campaigns/{id}/taverna', [WebViewController::class, 'liveTaverna'], [AuthMiddleware::class]);

// =============================================================================
// ROTAS DE API RESTful v1 (CONFORME ESPECIFICAÇÃO OPENAPI / RF01-RF26)
// =============================================================================

// 1. Autenticação & Usuários
$router->post('/api/v1/auth/register', [AuthController::class, 'register']);
$router->post('/api/v1/auth/login', [AuthController::class, 'login']);
$router->get('/api/v1/auth/me', [AuthController::class, 'me'], [AuthMiddleware::class]);
$router->post('/api/v1/auth/logout', [AuthController::class, 'logout']);

// 2. Campanhas & Gestão de Membros
$router->get('/api/v1/campaigns', [CampaignController::class, 'index'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns', [CampaignController::class, 'store'], [AuthMiddleware::class]);
$router->get('/api/v1/campaigns/{id}', [CampaignController::class, 'show'], [AuthMiddleware::class]);
$router->post('/api/v1/join', [CampaignController::class, 'join'], [AuthMiddleware::class]);
$router->get('/api/v1/campaigns/{id}/members', [CampaignController::class, 'members'], [AuthMiddleware::class]);

// 3. Fichas de Personagem & Rolagem de Dados D20 (Tormenta20 / D&D)
$router->get('/api/v1/campaigns/{id}/characters', [CharacterController::class, 'index'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns/{id}/characters', [CharacterController::class, 'store'], [AuthMiddleware::class]);
$router->get('/api/v1/characters/{id}', [CharacterController::class, 'show'], [AuthMiddleware::class]);
$router->patch('/api/v1/characters/{id}/status', [CharacterController::class, 'updateStatus'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns/{id}/dice/roll', [CharacterController::class, 'roll'], [AuthMiddleware::class]);
$router->get('/api/v1/campaigns/{id}/dice/history', [CharacterController::class, 'rollHistory'], [AuthMiddleware::class]);

// 4. Som & Ambientação Acústica Sincronizada (Exclusivo GM)
$router->get('/api/v1/campaigns/{id}/audio/library', [AudioController::class, 'library'], [AuthMiddleware::class]);
$router->get('/api/v1/campaigns/{id}/audio/live-state', [AudioController::class, 'liveState'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns/{id}/audio/sync', [AudioController::class, 'sync'], [AuthMiddleware::class, GMMiddleware::class]);

// 5. Crônicas Coletivas, Handouts & Reviravoltas da Trama (Exclusivo GM)
$router->get('/api/v1/campaigns/{id}/chronicles', [StoryController::class, 'chronicles'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns/{id}/chronicles', [StoryController::class, 'storeChronicle'], [AuthMiddleware::class]);
$router->get('/api/v1/campaigns/{id}/handouts', [StoryController::class, 'handouts'], [AuthMiddleware::class]);
$router->post('/api/v1/campaigns/{id}/plots/trigger', [StoryController::class, 'triggerPlotTwist'], [AuthMiddleware::class, GMMiddleware::class]);

// 6. Streaming em Tempo Real Server-Sent Events (SSE)
$router->get('/api/v1/campaigns/{id}/events/stream', [SSEStreamController::class, 'stream'], [AuthMiddleware::class]);

// Despacho da Requisição
$router->dispatch($request);
