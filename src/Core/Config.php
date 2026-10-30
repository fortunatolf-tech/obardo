<?php

declare(strict_types=1);

namespace OBardo\Core;

/**
 * Gestor Central de Configurações da Aplicação
 */
final class Config
{
    private static array $env = [];

    public static function load(string $envPath): void
    {
        if (!file_exists($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $val] = explode('=', $line, 2) + [null, ''];
            if ($key) {
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                self::$env[$key] = $val;
                $_ENV[$key] = $val;
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
