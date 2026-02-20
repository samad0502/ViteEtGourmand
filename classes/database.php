<?php

/**
 * Classe Database
 * Gère la connexion PDO à la base de données (Local & Production)
 */
class Database
{
    private static $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {

            // 1. On tente de récupérer l'URL complète (Standard JawsDB Heroku)
            $jawsUrl = getenv('JAWSDB_URL');

            if ($jawsUrl) {
                $url = parse_url($jawsUrl);
                $host     = $url['host'];
                $db_name  = substr($url['path'], 1);
                $user     = $url['user'];
                $password = $url['pass'];
                $port     = $url['port'] ?? '3306';
            } else {
                // 2. Sinon on prend les variables individuelles
                $host     = getenv('DB_HOST');
                $db_name  = getenv('DB_NAME');
                $user     = getenv('DB_USER');
                $password = getenv('DB_PASS') ?: getenv('DB_PASSWORD');
                $port     = getenv('DB_PORT') ?: '3306';

                // 3. Si on est en local (pas de host détecté), on charge le .env
                if (!$host) {
                    $envPath = __DIR__ . '/../.env';
                    if (file_exists($envPath)) {
                        $env = parse_ini_file($envPath);
                        $host     = $env['DB_HOST'] ?? 'localhost';
                        $db_name  = $env['DB_NAME'] ?? '';
                        $user     = $env['DB_USER'] ?? 'root';
                        $password = $env['DB_PASSWORD'] ?? '';
                    }
                }
            }

            // Sécurité pour Heroku : éviter de retomber sur localhost par erreur
            if ((!$host || $host === 'localhost') && getenv('HEROKU_APP_ID')) {
                die("Erreur fatale : L'hôte de la base de données n'est pas configuré sur l'environnement de production.");
            }

            $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $user,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (PDOException $e) {
                // Message d'erreur simplifié
                die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
            }
        }

        return self::$pdo;
    }
}
