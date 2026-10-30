<?php

declare(strict_types=1);

namespace OBardo\Core;

/**
 * Respostas HTTP Padronizadas, JSON RESTful, RFC 7807 e Server-Sent Events (SSE)
 */
final class Response
{
    public static function json(array $data, int $statusCode = 200, array $headers = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');

        foreach ($headers as $key => $val) {
            header("{$key}: {$val}");
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(
        string $title,
        string $detail,
        int $statusCode = 400,
        string $type = 'about:blank',
        array $invalidParams = []
    ): void {
        $problem = [
            'type' => $type,
            'title' => $title,
            'status' => $statusCode,
            'detail' => $detail,
            'instance' => $_SERVER['REQUEST_URI'] ?? '/',
        ];

        if (!empty($invalidParams)) {
            $problem['invalid_params'] = $invalidParams;
        }

        self::json($problem, $statusCode, [
            'Content-Type' => 'application/problem+json; charset=utf-8'
        ]);
    }

    public static function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    public static function sseHeaders(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-transform');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Essencial para Nginx FastCGI não reter pacotes
        ob_implicit_flush(true);
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
    }
}
