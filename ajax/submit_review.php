<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/database.php';

$userRole = $_SESSION['user']['role_name'] ?? '';

if (!isset($_SESSION['user']) || ($userRole !== 'employee' && $userRole !== 'admin')) {
    die("Accès refusé");
}

$database = new Database();
$db = $database->getConnection();

// ---  VALIDATION D'UN AVIS (NOTE ET COMMENTAIRE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'validate_review') {
    $reviewId = (int)$_POST['review_id'];

    $stmt = $db->prepare("UPDATE reviews SET is_published = 1 WHERE id = ?");
    if ($stmt->execute([$reviewId])) {
        header("Location: ../employee/employee_dashboard.php?msg=review_validated");
    } else {
        header("Location: ../employee/employee_dashboard.php?msg=error");
    }
    exit;
}

// --- MISE À JOUR DU STATUT COMMANDE  ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];

    try {
        // Mise à jour MySQL
        $stmt = $db->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);

        // Si Terminé -> Envoi Mail avec PHPMailer
        if ($newStatus === 'finished') {
        }

        header("Location: ../employee/employee_dashboard.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
