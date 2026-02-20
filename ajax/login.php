<?php
session_start();
header('Content-Type: application/json');

//  Connexion à la base de données
require_once '../classes/database.php';
$database = new Database();
$db = $database->getConnection();

$response = ['success' => false, 'message' => 'Erreur de connexion'];

// Récupération des données POST
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';



if (!empty($email) && !empty($password)) {
    // Recherche de l'utilisateur avec son role
    $query = "SELECT u.*, r.name as role_name 
              FROM users u 
              JOIN roles r ON u.role_id = r.id 
              WHERE u.email = ?";

    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification
    if ($user && password_verify($password, $user['password'])) {
        // On enregistre TOUT en session
        $_SESSION['user'] = [
            'id'        => $user['id'],
            'firstname' => $user['firstname'],
            'lastname'  => $user['lastname'],
            'email'     => $user['email'],
            'role'      => $user['role_name'],
            'phone'     => $user['phone']
        ];

        $response['success'] = true;
        $response['role'] = $user['role_name'];
        $response['message'] = 'Connexion réussie';
    } else {
        $response['message'] = 'Identifiants incorrects';
    }
}
// Envoi de la réponse JSON au JavaScript
echo json_encode($response);
exit;
