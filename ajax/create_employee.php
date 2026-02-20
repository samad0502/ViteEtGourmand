<?php
session_start();

require_once '../classes/database.php';
require_once '../classes/MailHandler.php';

// Correction de la vérification du rôle (on s'aligne sur role_name utilisé ailleurs)
$userRole = $_SESSION['user']['role_name'] ?? $_SESSION['user']['role'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userRole === 'admin') {
    $db = (new Database())->getConnection();

    // Nettoyage basique des entrées
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname  = htmlspecialchars(trim($_POST['lastname']));
    $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../admin/admin_users.php?error=invalid_email');
        exit;
    }

    try {
        // Insertion de l'employé (role_id 2)
        $stmt = $db->prepare("INSERT INTO users (firstname, lastname, email, password, role_id, is_active) VALUES (?, ?, ?, ?, 2, 1)");
        $stmt->execute([$firstname, $lastname, $email, $password]);

        // Envoi du mail SANS le mot de passe (Exigence José)
        try {
            MailHandler::sendEmployeeWelcome($email, $firstname);
        } catch (Exception $mailEx) {
            // On log l'erreur de mail mais on ne bloque pas la création du compte
            error_log("Erreur envoi mail employé : " . $mailEx->getMessage());
        }

        header('Location: ../admin/admin_users.php?success=1');
        exit;
    } catch (PDOException $e) {
        // Si l'email existe déjà (clé UNIQUE en base)
        header('Location: ../admin/admin_users.php?error=already_exists');
        exit;
    }
} else {
    // Tentative d'accès direct ou non autorisé
    header('Location: ../admin/admin_users.php?error=unauthorized');
    exit;
}
