<?php

/**
 * Endpoint AJAX / POST
 * Validation finale de la commande depuis panier.php
 */

session_start();

header('Content-Type: application/json');


//  SÉCURITÉ : UTILISATEUR CONNECTÉ


if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non authentifié'
    ]);
    exit;
}


//  VÉRIFICATION DES DONNÉES


$requiredFields = [
    'menu_id',
    'number_people',
    'equipment_ready',
    'delivery_address',
    'delivery_date',
    'delivery_time'
];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Champ manquant : $field"
        ]);
        exit;
    }
}


//  NETTOYAGE & CAST DES DONNÉES


$data = [
    'menu_id'          => (int) $_POST['menu_id'],
    'number_people'    => (int) $_POST['number_people'],
    'equipment_ready'  => (int) $_POST['equipment_ready'],
    'delivery_address' => trim($_POST['delivery_address']),
    'delivery_date'    => $_POST['delivery_date'],
    'delivery_time'    => $_POST['delivery_time'],
    'user_id'          => (int) $_SESSION['user']['id']
];





require_once '../classes/Order.php';

$order = new Order();
$success = $order->create($data);





if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Commande validée avec succès'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la création de la commande'
    ]);
}
