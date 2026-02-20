<?php
session_start();
require_once '../classes/database.php';

if (isset($_GET['id']) && isset($_SESSION['user'])) {
    $db = (new Database())->getConnection();
    $orderId = $_GET['id'];
    $userId = $_SESSION['user']['id'];

    // Vérifier que la commande appartient à l'user et est bien en 'pending'
    $stmt = $db->prepare("SELECT menu_id, number_people FROM orders WHERE id = ? AND user_id = ? AND order_status = 'pending'");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if ($order) {
        try {
            $db->beginTransaction();

            // Passer le statut à 'cancelled'
            $update = $db->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
            $update->execute([$orderId]);

            // RENDRE LE STOCK (On rajoute les portions annulées)
            $restock = $db->prepare("UPDATE menus SET remaining_quantity = remaining_quantity + 1 WHERE id = ?");
            $restock->execute([$order['menu_id']]);

            $db->commit();
            header('Location: ../orders.php?msg=cancelled');
        } catch (Exception $e) {
            $db->rollBack();
            header('Location: ../orders.php?error=cancel_failed');
        }
    } else {
        header('Location: ../orders.php?error=not_allowed');
    }
}
