<?php
require_once '../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Vérification de la correspondance des mots de passe
    if ($password !== $confirm_password) {
        header("Location: reset_password.php?token=$token&error=match");
        exit;
    }

    $db = (new Database())->getConnection();

    // Vérifier si le token est toujours valide
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Haschage du nouveau mot de passe et nettoyage du token
        $newHash = password_hash($password, PASSWORD_DEFAULT);

        $update = $db->prepare("
            UPDATE users 
            SET password = ?, 
                reset_token = NULL, 
                reset_expires_at = NULL 
            WHERE id = ?
        ");

        if ($update->execute([$newHash, $user['id']])) {
            header("Location: login.php?msg=password_updated");
            exit;
        }
    } else {
        header("Location: forgot_password.php?error=invalid_token");
        exit;
    }
}
