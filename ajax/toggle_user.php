<?php
session_start();
require_once '../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user']['role_name'] === 'admin') {
    $db = (new Database())->getConnection();
    $userId = $_POST['user_id'];

    // On bascule l'état is_active (si c'est 1 ça devient 0, si c'est 0 ça devient 1)
    $stmt = $db->prepare("UPDATE users SET is_active = 1 - is_active WHERE id = ? AND role_id != 1");
    $stmt->execute([$userId]);

    header('Location: ../admin/admin_users.php?msg=status_updated');
} else {
    header('Location: ../index.php');
}
