<?php

declare(strict_types=1);

namespace OBardo\Tests\Unit;

use PHPUnit\Framework\TestCase;
use OBardo\Core\Router;
use OBardo\Core\Request;

/**
 * Testes Unitários do Roteador RESTful e Pipeline de Middlewares
 */
final class RouterAndMiddlewareTest extends TestCase
{
    public function testRouterMatchesGetRouteWithDynamicParameters(): void
    {
        $router = new Router();
        $called = false;
        $capturedParams = [];

        $router->get('/api/v1/campaigns/{id}/members/{user_id}', function (Request $req, array $params) use (&$called, &$capturedParams) {
            $called = true;
            $capturedParams = $params;
        });

        // Simula request GET
        $request = new Request('GET', '/api/v1/campaigns/42/members/108');
        $router->dispatch($request);

        $this->assertTrue($called, 'Rota com parâmetros dinâmicos deve ser despachada');
        $this->assertSame(['id' => '42', 'user_id' => '108'], $capturedParams);
    }

    public function testRouterMatchesPatchAndDeleteMethods(): void
    {
        $router = new Router();
        $patchCalled = false;
        $deleteCalled = false;

        $router->patch('/api/v1/campaigns/{id}/secrets', function () use (&$patchCalled) {
            $patchCalled = true;
        });

        $router->delete('/api/v1/campaigns/{id}/members/{user_id}', function () use (&$deleteCalled) {
            $deleteCalled = true;
        });

        $patchReq = new Request('PATCH', '/api/v1/campaigns/7/secrets');
        $router->dispatch($patchReq);
        $this->assertTrue($patchCalled);

        $deleteReq = new Request('DELETE', '/api/v1/campaigns/7/members/99');
        $router->dispatch($deleteReq);
        $this->assertTrue($deleteCalled);
    }

    public function testHeadMethodDispatchesGetHandler(): void
    {
        $router = new Router();
        $headGetDispatched = false;

        $router->get('/health', function () use (&$headGetDispatched) {
            $headGetDispatched = true;
        });

        $headReq = new Request('HEAD', '/health');
        $router->dispatch($headReq);
        $this->assertTrue($headGetDispatched, 'Requisições HEAD devem despachar a rota GET correspondente');
    }
}
