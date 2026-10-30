<?php

declare(strict_types=1);

namespace OBardo\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexão Segura Singleton com MariaDB / MySQL via PDO
 */
final class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = Config::get('DB_HOST', '127.0.0.1');
        $port = Config::get('DB_PORT', '3306');
        $database = Config::get('DB_DATABASE', 'sql_obardo_com_br');
        $username = Config::get('DB_USERNAME', 'sql_obardo_com_br');
        $password = Config::get('DB_PASSWORD', '36af5bfb0d1c3');
        $charset = Config::get('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

        try {
            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Elimina SQL Injection
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Falha na conexão com o banco de dados: " . $e->getMessage(), (int)$e->getCode(), $e);
        }

        return self::$instance;
    }
}
