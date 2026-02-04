<?php

/**
 * Charge les variables depuis le fichier .env
 */
$lines = file(__DIR__ . '/../.env');

foreach ($lines as $line) {
    if (trim($line) && !str_starts_with($line, '#')) {
        [$key, $value] = explode('=', trim($line), 2);
        $_ENV[$key] = $value;
    }
}
