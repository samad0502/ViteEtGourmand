<?php

/**
 * Classe Menu
 * Contient la logique métier liée aux menus
 */

require_once 'Database.php';

class Menu
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne les menus selon les filtres
     */
    public function getAll(array $filters)
    {

        $sql = "SELECT * FROM menus WHERE 1=1";
        $params = [];

        if ($filters['prixMin']) {
            $sql .= " AND prix >= ?";
            $params[] = $filters['prixMin'];
        }

        if ($filters['prixMax']) {
            $sql .= " AND prix <= ?";
            $params[] = $filters['prixMax'];
        }

        if ($filters['fourchette']) {
            [$min, $max] = explode('-', $filters['fourchette']);
            $sql .= " AND prix BETWEEN ? AND ?";
            $params[] = $min;
            $params[] = $max;
        }

        if ($filters['personnesMin']) {
            $sql .= " AND personnes >= ?";
            $params[] = $filters['personnesMin'];
        }

        if ($filters['theme']) {
            $sql .= " AND theme = ?";
            $params[] = $filters['theme'];
        }

        if ($filters['regime']) {
            $sql .= " AND regime = ?";
            $params[] = $filters['regime'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne un menu par son identifiant
     */
    public function getById(int $id)
    {

        $stmt = $this->pdo->prepare(
            "SELECT * FROM menus WHERE id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
