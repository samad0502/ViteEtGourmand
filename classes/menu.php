<?php

/**
 * Classe Menu
 * Gère toutes les opérations liées aux menus
 */

require_once __DIR__ . '/database.php';

class Menu
{
    /**
     * Instance PDO
     */
    private PDO $pdo;

    /**
     * Constructeur
     * Initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère tous les menus (sans filtres)
     * Utilisé au chargement initial
     */
    public function getAllMenus(): array
    {
        $sql = "SELECT * FROM menus ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        $menus = $stmt->fetchAll();

        foreach ($menus as &$menu) {
            // Découpe les images
            $images = explode(',', $menu['image']);

            // Image principale = première image
            $menu['main_image'] = trim($images[0]);
        }

        return $menus;
    }


    /**
     * Récupère les menus avec filtres
     * Utilisé par l'AJAX
     */
    public function getFilteredMenus(array $filters): array
    {
        $sql = "SELECT * FROM menus WHERE 1=1";
        $params = [];

        // Filtre prix minimum
        if (!empty($filters['priceMin'])) {
            $sql .= " AND price >= ?";
            $params[] = $filters['priceMin'];
        }

        // Filtre prix maximum
        if (!empty($filters['priceMax'])) {
            $sql .= " AND price <= ?";
            $params[] = $filters['priceMax'];
        }

        // Filtre nombre minimum de personnes
        if (!empty($filters['minPeople'])) {
            $sql .= " AND min_people >= ?";
            $params[] = $filters['minPeople'];
        }

        // Filtre thème
        if (!empty($filters['theme'])) {
            $sql .= " AND theme_id = ?";
            $params[] = $filters['theme'];
        }

        // Filtre régime
        if (!empty($filters['diet'])) {
            $sql .= " AND diet_id = ?";
            $params[] = $filters['diet'];
        }

        // Exécution de la requête
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        //  on récupère les menus
        $menus = $stmt->fetchAll();

        //  on ajoute l’image principale à chaque menu
        foreach ($menus as &$menu) {
            $images = explode(',', $menu['image']);
            $menu['main_image'] = trim($images[0]);
        }

        //  on retourne les menus enrichis
        return $menus;
    }


    /**
     * Récupère un menu par son ID
     * Utilisé pour la page menu_detail.php
     */
    public function getMenuById(int $id): array|false
    {
        $sql = "SELECT * FROM menus WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);

        $menu = $stmt->fetch();

        if (!$menu) {
            return false;
        }

        //  TRANSFORMATION DES IMAGES POUR LE CAROUSEL
        // On transforme "img1.jpg,img2.jpg" → tableau JS
        $menu['images'] = array_map(
            'trim',
            explode(',', $menu['image'])
        );

        return $menu;
    }
}
