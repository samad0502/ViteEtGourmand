<?php

/**
 * Contrôleur AJAX
 * Appelle la logique métier et retourne du JSON
 */

require '../classes/Menu.php';

$menu = new Menu();

// Récupération des filtres
$filters = [
    'prixMin' => $_GET['prixMin'] ?? null,
    'prixMax' => $_GET['prixMax'] ?? null,
    'fourchette' => $_GET['fourchette'] ?? null,
    'personnesMin' => $_GET['personnesMin'] ?? null,
    'theme' => $_GET['theme'] ?? null,
    'regime' => $_GET['regime'] ?? null
];

echo json_encode($menu->getAll($filters));
