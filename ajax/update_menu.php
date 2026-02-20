<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = (new Database())->getConnection();
        $id = (int)$_POST['menu_id'];
        $title = htmlspecialchars($_POST['title']);
        $price = (float)$_POST['price'];
        $desc = htmlspecialchars($_POST['description']);

        // Gestion de l'image
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "../assets/img/" . $imageName);

            // Mise à jour AVEC nouvelle image
            $stmt = $db->prepare("UPDATE menus SET title = ?, price = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $price, $desc, $imageName, $id]);
        } else {
            // Mise à jour SANS changer l'image
            $stmt = $db->prepare("UPDATE menus SET title = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $price, $desc, $id]);
        }

        header("Location: ../employee/employee_dashboard.php?msg=menu_updated#menus-pane");
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
