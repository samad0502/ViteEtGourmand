<?php

/**
 * Classe Order
 * Gère toutes les opérations liées aux commandes
 */

require_once __DIR__ . '/database.php';

class Order
{
    /**
     * Instance PDO
     */
    private PDO $pdo;

    /**
     * Constructeur
     * Initialise la connexion à la base
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Crée une commande complète depuis le panier
     */
    public function create(array $data): bool
    {
        /**
         *  Récupération du menu (sécurité serveur)
         */
        $stmt = $this->pdo->prepare("
            SELECT price, min_people, remaining_quantity
            FROM menus
            WHERE id = ?
        ");
        $stmt->execute([$data['menu_id']]);
        $menu = $stmt->fetch();

        // Menu inexistant ou stock insuffisant
        if (
            !$menu ||
            $data['number_people'] < $menu['min_people'] ||
            $menu['remaining_quantity'] < $data['number_people']
        ) {
            return false;
        }

        /**
         *  Calcul du prix total
         */
        $totalPrice = $menu['price'] * $data['number_people'];

        //  Réduction 10% si +5 personnes au minimum
        if ($data['number_people'] >= $menu['min_people'] + 5) {
            $totalPrice *= 0.9;
        }

        /**
         *  Transaction SQL
         */
        $this->pdo->beginTransaction();

        try {

            /**
             *  Génération numéro de commande
             */
            $orderNumber = uniqid('ORD-');

            /**
             *  Insertion de la commande
             */
            $insert = $this->pdo->prepare("
                INSERT INTO orders (
                    order_number,
                    order_date,
                    order_status,
                    number_people,
                    equipment_ready,
                    delivery_address,
                    delivery_date,
                    delivery_time,
                    total_price,
                    user_id,
                    menu_id
                )
                VALUES (?, NOW(), 'pending', ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insert->execute([
                $orderNumber,
                $data['number_people'],
                $data['equipment_ready'],
                $data['delivery_address'],
                $data['delivery_date'],
                $data['delivery_time'],
                $totalPrice,
                $data['user_id'],
                $data['menu_id']
            ]);

            /**
             *  Mise à jour du stock
             */
            $update = $this->pdo->prepare("
                UPDATE menus
                SET remaining_quantity = remaining_quantity - ?
                WHERE id = ?
            ");
            $update->execute([
                $data['number_people'],
                $data['menu_id']
            ]);

            /**
             *  Validation
             */
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {

            /**
             *  Annulation si erreur
             */
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Récupère les commandes d’un utilisateur
     */
    public function getByUser(int $userId): array
    {
        $sql = "
            SELECT
                o.id,
                o.order_number,
                o.order_date,
                o.order_status,
                o.number_people,
                o.equipment_ready,
                o.total_price,

                m.title,
                m.price
            FROM orders o
            INNER JOIN menus m ON o.menu_id = m.id
            WHERE o.user_id = ?
            ORDER BY o.order_date DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
}
