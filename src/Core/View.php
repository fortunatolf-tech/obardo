<?php

declare(strict_types=1);

namespace OBardo\Core;

use RuntimeException;

/**
 * Renderizador de Visualizações HTML com Sanitização XSS Nativa
 */
final class View
{
    private static string $baseDir = __DIR__ . '/../../templates';

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    public static function render(string $viewPath, array $data = []): void
    {
        $file = self::$baseDir . '/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("Arquivo de visualização não encontrado: {$file}");
        }

        extract($data);

        // Função auxiliar de escape para templates
        $e = static fn(mixed $value): string => self::escape($value);

        header('Content-Type: text/html; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');

        require $file;
        exit;
    }
}
