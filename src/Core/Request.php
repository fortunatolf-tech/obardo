<?php

declare(strict_types=1);

namespace OBardo\Core;

/**
 * Abstração de Requisições HTTP com Sanitização e Validação Nativa PHP 8.3
 */
final readonly class Request
{
    public string $method;
    public string $uri;
    public string $path;
    public array $queryParams;
    public array $body;
    public array $headers;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        $this->queryParams = $_GET;

        // Leitura de cabeçalhos
        $headers = [];
        foreach ($_SERVER as $key => $val) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $val;
            }
        }
        $this->headers = $headers;

        // Leitura e validação segura de Body JSON (PHP 8.3 json_validate)
        $body = [];
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            if (function_exists('json_validate') && json_validate($raw)) {
                $body = json_decode($raw, true) ?? [];
            } else {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $body = $decoded ?? [];
                }
            }
        }

        // Suporte a formulários POST normais
        if ($this->method === 'POST' && empty($body) && !empty($_POST)) {
            $body = $_POST;
        }

        $this->body = $body;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function getBearerToken(): ?string
    {
        $auth = $this->headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if ($auth && preg_match('/Bearer\s+(\S+)/i', $auth, $matches)) {
            return $matches[1];
        }

        // Fallback para cookie de sessão ou query string (SSE/WebSockets)
        return $_COOKIE['bardo_token'] ?? $this->queryParams['token'] ?? null;
    }
}
