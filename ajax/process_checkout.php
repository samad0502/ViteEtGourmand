<?php
session_start();
require_once '../classes/database.php';
require_once '../vendor/autoload.php';
require_once '../classes/MailHandler.php';

if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header('Location: ../panier.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = $_SESSION['user'];

// Données du formulaire
$city = trim($_POST['city'] ?? '');
$distance = (float)($_POST['distance'] ?? 0);
$address = trim($_POST['address'] ?? '');
$delivery_date = $_POST['delivery_date'] ?? '';
$delivery_time = $_POST['delivery_time'] ?? '';

// INITIALISATION DU TOTAL GLOBAL
$totalCommandeComplete = 0;

try {
    $db->beginTransaction();
    $groupOrderNumber = 'ORD-' . strtoupper(uniqid());

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $db->prepare("SELECT price, min_people FROM menus WHERE id = ?");
        $stmt->execute([$item['menu_id']]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$menu) continue;

        $nbPers = (int)$item['number_people'];
        $pricePerPerson = (float)$menu['price'];
        $subtotal = $pricePerPerson * $nbPers;

        if ($nbPers >= ($menu['min_people'] + 5)) {
            $subtotal *= 0.90;
        }

        $deliveryFees = 0;
        if (strtolower($city) !== 'bordeaux') {
            $deliveryFees = 5 + (0.59 * $distance);
        }

        $finalLinePrice = $subtotal + $deliveryFees;

        // ON AJOUTE AU TOTAL GLOBAL
        $totalCommandeComplete += $finalLinePrice;

        $query = "INSERT INTO orders (
                    order_number, order_status, number_people, equipment_ready, 
                    user_id, menu_id, delivery_address, delivery_date, 
                    delivery_time, total_price
                ) VALUES (?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtOrder = $db->prepare($query);
        $stmtOrder->execute([
            $groupOrderNumber,
            $nbPers,
            $item['equipment_ready'],
            $user['id'],
            $item['menu_id'],
            $address . " (" . $city . ")",
            $delivery_date,
            $delivery_time,
            $finalLinePrice
        ]);

        $db->prepare("UPDATE menus SET remaining_quantity = remaining_quantity - 1 WHERE id = ?")
            ->execute([$item['menu_id']]);
    }

    $db->commit();

    // ENVOI DE L'EMAIL APRES LE COMMIT (Une seule fois !)

    MailHandler::sendOrderConfirmation(
        $user['email'],
        $user['firstname'],
        $groupOrderNumber,
        $totalCommandeComplete,
        $_SESSION['cart'],
        $delivery_date
    );


    unset($_SESSION['cart']);

    // On redirige vers order_success.php en passant le numéro de commande dans l'URL
    header('Location: ../order_success.php?order_ref=' . $groupOrderNumber);
    exit;



    unset($_SESSION['cart']);
    header('Location: ../orders.php?success=1');
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    die("Erreur critique : " . $e->getMessage());
}
