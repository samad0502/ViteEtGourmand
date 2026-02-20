<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

// Vérification de sécurité : seul un employé ou admin peut supprimer
$userRole = $_SESSION['user']['role_name'] ?? '';
if (!isset($_SESSION['user']) || ($userRole !== 'employee' && $userRole !== 'admin')) {
    header('Location: ../index.php?error=access_denied');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $menuId = (int)$_POST['menu_id'];

        // On tente la suppression
        $stmt = $db->prepare("DELETE FROM menus WHERE id = ?");
        $stmt->execute([$menuId]);

        // Redirection avec message de succès
        header("Location: ../employee/employee_dashboard.php?msg=menu_deleted#menus-pane");
        exit;
    } catch (PDOException $e) {
        // Gestion de l'erreur si le menu est lié à des commandes 
        if ($e->getCode() == '23000') {
            header("Location: ../employee/employee_dashboard.php?error=menu_linked#menus-pane");
        } else {
            header("Location: ../employee/employee_dashboard.php?error=delete_failed#menus-pane");
        }
        exit;
    }
}
