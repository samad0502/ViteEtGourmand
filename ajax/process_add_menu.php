<?php
session_start();

// Connexion à la configuration et à la base de données

require_once '../config/load_env.php';
require_once '../classes/database.php';

// Vérification de sécurité (uniquement employé ou admin)
$userRole = $_SESSION['user']['role_name'] ?? $_SESSION['user']['role'] ?? '';
if (!isset($_SESSION['user']) || ($userRole !== 'employee' && $userRole !== 'admin')) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->getConnection();

    // Récupération et nettoyage des données
    $title = htmlspecialchars($_POST['title']);
    $price = floatval($_POST['price']);
    $min_people = intval($_POST['min_people']);
    $description = htmlspecialchars($_POST['description']);

    // --- GESTION DE L'IMAGE ---
    $imageName = "default_menu.jpg";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../assets/img/menus/";

        // Création d'un nom de fichier unique pour éviter les doublons
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $imageName = time() . "_" . uniqid() . "." . $extension;
        $targetFile = $targetDir . $imageName;

        // On déplace le fichier du dossier temporaire vers assets/img/
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Si l'upload échoue, on peut rediriger avec une erreur
            header("Location: ../employee/add_menu.php?status=upload_error");
            exit;
        }
    }

    // --- INSERTION EN BASE DE DONNÉES ---
    try {
        $query = "INSERT INTO menus (title, description, price, min_people, image) 
                  VALUES (:title, :description, :price, :min_people, :image)";
        $stmt = $db->prepare($query);

        $success = $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':price'       => $price,
            ':min_people'  => $min_people,
            ':image'       => $imageName
        ]);

        if ($success) {
            // Redirection vers le dashboard avec un message de succès
            header("Location: ../employee/dashboard.php?status=added");
        } else {
            header("Location: ../employee/add_menu.php?status=error");
        }
    } catch (PDOException $e) {
        // En cas d'erreur SQL (ex: colonne manquante)
        die("Erreur SQL : " . $e->getMessage());
    }
    exit;
}
