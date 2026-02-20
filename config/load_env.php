<?php

/**
 * Chargeur de variables d'environnement
 */
// On définit le chemin absolu vers la racine du projet pour trouver le .env
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) continue;

        // Séparer clé=valeur
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // On définit dans $_ENV et getenv()
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}
