<?php
session_start();
require_once '../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $db = (new Database())->getConnection();
    $userId = $_SESSION['user']['id'];

    // Nettoyage des données
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);

    // Mise à jour SQL
    $sql = "UPDATE users SET firstname = ?, lastname = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = $db->prepare($sql);

    if ($stmt->execute([$firstname, $lastname, $phone, $address, $userId])) {
        // MAJ de la SESSION pour que le changement de nom soit visible direct dans la navbar
        $_SESSION['user']['firstname'] = $firstname;
        $_SESSION['user']['lastname']  = $lastname;
        $_SESSION['user']['phone']     = $phone;
        $_SESSION['user']['address']   = $address;

        header('Location: ../profile.php?success=1');
    } else {
        header('Location: ../profile.php?error=update_failed');
    }
}
