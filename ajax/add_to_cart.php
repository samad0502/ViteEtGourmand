<?php
session_start();
header('Content-Type: application/json');

// Vérification de la session utilisateur
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Connexion requise']);
    exit;
}

// Récupération et nettoyage des données
$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
$number_people = isset($_POST['number_people']) ? (int)$_POST['number_people'] : 0;
$equipment_ready = isset($_POST['equipment_ready']) ? (int)$_POST['equipment_ready'] : 0;

if ($menu_id <= 0 || $number_people <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Initialisation du panier en session s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajout au panier (On peut vérifier ici si le menu est déjà présent pour cumuler)

$_SESSION['cart'][] = [
    'menu_id' => $menu_id,
    'number_people' => $number_people,
    'equipment_ready' => $equipment_ready,
    'added_at' => date('Y-m-d H:i:s')
];

echo json_encode([
    'success' => true,
    'message' => 'Menu ajouté au panier',
    'cart_count' => count($_SESSION['cart'])
]);
