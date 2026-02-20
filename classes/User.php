<?php

/**
 * Classe User
 * Gère l'inscription et la connexion des utilisateurs
 */

require_once __DIR__ . '/database.php';

class User
{
    private ?PDO $pdo; // Le "?" permet à PDO d'être nul si la connexion échoue

    public function __construct()
    {
        // Connexion à la base de données via la méthode statique
        $this->pdo = Database::getConnection();

        // Sécurité : Si la connexion a échoué (null), on s'arrête proprement
        if (!$this->pdo) {
            throw new Exception("Erreur interne : Impossible d'établir une connexion avec la base de données.");
        }
    }

    /**
     * Inscription d’un utilisateur
     */
    public function register(array $data): bool
    {
        // Hash du mot de passe
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Préparation de la requête
        $stmt = $this->pdo->prepare("
            INSERT INTO users 
            (firstname, lastname, address, city, zip_code, phone, email, password, role_id, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $roleId   = $data['role_id'] ?? 3;
        $isActive = $data['is_active'] ?? 1;

        return $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['address'],
            $data['city'],
            $data['zip_code'],
            $data['phone'],
            $data['email'],
            $passwordHash,
            $roleId,
            $isActive
        ]);
    }

    /**
     * Connexion d’un utilisateur
     */
    public function login($email, $password)
    {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.email = :email 
                AND u.is_active = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
