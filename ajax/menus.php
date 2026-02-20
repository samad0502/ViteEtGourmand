<?php

/**
 * Endpoint AJAX
 * Retourne les menus avec ou sans filtres
 */

require_once '../classes/menu.php';

// Récupération des filtres envoyés en GET
$filters = [
    'priceMin'  => $_GET['priceMin'] ?? null,
    'priceMax'  => $_GET['priceMax'] ?? null,
    'minPeople' => $_GET['minPeople'] ?? null,
    'theme'     => $_GET['theme'] ?? null,
    'diet'      => $_GET['diet'] ?? null
];

$menu = new Menu();

// Si au moins un filtre est présent → filtrage
if (array_filter($filters)) {
    $result = $menu->getFilteredMenus($filters);
}
// Sinon → tous les menus
else {
    $result = $menu->getAllMenus();
}

// Retour JSON
header('Content-Type: application/json');
echo json_encode($result);
