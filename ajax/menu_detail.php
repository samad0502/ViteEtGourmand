<?php

/**
 * Endpoint AJAX
 * Retourne le détail d'un menu
 */

require_once '../classes/menu.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Menu ID manquant']);
    exit;
}

$menu = new Menu();
$result = $menu->getMenuById((int) $_GET['id']);

if (!$result) {
    http_response_code(404);
    echo json_encode(['error' => 'Menu introuvable']);
    exit;
}

header('Content-Type: application/json');
echo json_encode($result);
