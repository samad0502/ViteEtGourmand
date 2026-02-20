<?php

session_start();
require_once '../classes/database.php';

// Vérification de la session
if (!isset($_SESSION['user']) || !isset($_SESSION['cart'])) {
    header('Location: ../menus.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();

    // 1. Récupération des données du formulaire panier.php
    $userId = $_SESSION['user']['id'];
    $menuId = (int)$_POST['menu_id'];
    $numPeople = (int)$_POST['number_people'];
    $equipment = (int)$_POST['equipment_ready'];

    $address = htmlspecialchars($_POST['delivery_address']);
    $deliveryDate = $_POST['delivery_date'];
    $deliveryTime = $_POST['delivery_time'];

    // Le prix total envoyé par le JS (sécurisé par une re-vérification si besoin)
    $totalPrice = (float)$_POST['final_total_price'];

    // 2. Génération du numéro de commande unique 
    $orderNumber = "VG-" . date('Y') . "-" . strtoupper(substr(md5(uniqid()), 0, 5));

    try {
        // Insertion dans la table 'orders' 
        $sql = "INSERT INTO orders (
                    order_number, 
                    user_id, 
                    menu_id, 
                    number_people, 
                    delivery_address, 
                    delivery_date, 
                    delivery_time, 
                    total_price, 
                    equipment_ready, 
                    order_status,
                    order_date
                ) VALUES (:num, :user, :menu, :people, :addr, :d_date, :d_time, :total, :equip, 'En attente', NOW())";

        $stmt = $db->prepare($sql);

        $params = [
            ':num'    => $orderNumber,
            ':user'   => $userId,
            ':menu'   => $menuId,
            ':people' => $numPeople,
            ':addr'   => $address,
            ':d_date' => $deliveryDate,
            ':d_time' => $deliveryTime,
            ':total'  => $totalPrice,
            ':equip'  => $equipment
        ];

        if ($stmt->execute($params)) {
            $newOrderId = $db->lastInsertId();

            // Nettoyage du panier après succès
            unset($_SESSION['cart']);

            // Redirection vers la page de succès 
            header("Location: ../order_success.php?id=" . $newOrderId);
            exit;
        } else {
            echo "Une erreur est survenue lors de l'enregistrement de la commande.";
        }
    } catch (PDOException $e) {
        die("Erreur base de données : " . $e->getMessage());
    }
}
