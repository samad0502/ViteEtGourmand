<?php

/**
 * Contrôleur AJAX
 * Retourne le détail d’un menu en JSON
 */

require '../classes/Menu.php';

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$menu = new Menu();

// Appel de la logique métier
$result = $menu->getById((int) $_GET['id']);

// Retour JSON
echo json_encode($result);
