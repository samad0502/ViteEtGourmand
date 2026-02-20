<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

// On vérifie que c'est bien un utilisateur connecté
$userRole = $_SESSION['user']['role_name'] ?? '';

if (!isset($_SESSION['user']) || $userRole !== 'utilisateur') {
    die("Accès refusé : Seuls les clients peuvent laisser un avis.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $userId  = (int)$_SESSION['user']['id'];
    $rating  = (int)$_POST['rating'];
    $comment = htmlspecialchars($_POST['comment']);

    try {
        $db = (new Database())->getConnection();

        // On insère l'avis (is_published = 0 car l'employé doit valider ensuite)
        $stmt = $db->prepare("INSERT INTO reviews (order_id, user_id, rating, comment, is_published) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$orderId, $userId, $rating, $comment]);

        header("Location: ../orders.php?success=review_sent");
        exit;
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
