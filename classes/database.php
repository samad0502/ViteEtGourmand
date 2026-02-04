<?php

/**
 * Classe Database
 * Fournit une connexion PDO unique
 */

require_once __DIR__ . '/../config/env.php';

class Database
{

    private static $pdo = null;

    public static function getConnection()
    {

        if (self::$pdo === null) {

            self::$pdo = new PDO(
                "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
                $_ENV['DB_USER'],
                $_ENV['DB_PASSWORD'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$pdo;
    }
}
