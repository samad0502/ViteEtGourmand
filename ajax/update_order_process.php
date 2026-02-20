<?php
session_start();
require_once __DIR__ . '/../classes/database.php';
require_once '../config/load_env.php';


$possible_paths = [
    __DIR__ . '/../classes/PHPMailer/src/',
    __DIR__ . '/../classes/PHPMailer-master/src/',
    __DIR__ . '/../classes/PHPMailer/',
    __DIR__ . '/../vendor/phpmailer/phpmailer/src/'
];

$found_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path . 'Exception.php')) {
        $found_path = $path;
        break;
    }
}

if (!$found_path) {
    die("Erreur critique : PHPMailer est introuvable.");
}

require_once $found_path . 'Exception.php';
require_once $found_path . 'PHPMailer.php';
require_once $found_path . 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $db = (new Database())->getConnection();
    $orderId = $_POST['order_id'];

    // RÉCUPÉRATION DES ANCIENNES INFOS
    $stmtOld = $db->prepare("SELECT order_number, delivery_address, delivery_date, delivery_time, equipment_ready FROM orders WHERE id = ?");
    $stmtOld->execute([$orderId]);
    $old = $stmtOld->fetch();

    if (!$old) {
        exit("Commande introuvable");
    }

    // MISE À JOUR (Logistique uniquement)
    $sql = "UPDATE orders SET 
        delivery_address = ?, 
        delivery_date = ?, 
        delivery_time = ?, 
        equipment_ready = ?,
        is_modified_by_client = 1 
        WHERE id = ? AND order_status = 'pending'";

    $stmtUpdate = $db->prepare($sql);
    $success = $stmtUpdate->execute([
        $_POST['delivery_address'],
        $_POST['delivery_date'],
        $_POST['delivery_time'],
        $_POST['equipment_ready'],
        $orderId
    ]);

    // ENVOI DU MAIL DÉTAILLÉ
    if ($success) {
        $mail = new PHPMailer(true);
        try {
            // Configuration Serveur
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST') ?: 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT') ?: 2525;
            $mail->CharSet    = 'UTF-8';

            // On utilise une adresse fixe ou celle de la session
            $senderEmail = $_SESSION['user']['email'] ?? 'noreply@vite-gourmand.fr';
            $senderName  = $_SESSION['user']['name'] ?? 'Client Vite & Gourmand';

            $mail->setFrom($senderEmail, $senderName);
            $mail->addAddress('admin@vite-gourmand.fr', 'Admin Vite & Gourmand');

            $mail->isHTML(true);
            $mail->Subject = "⚠️ MODIFICATION Commande #" . $old['order_number'];

            // Corps du mail
            $body = "<h2>Modification de la commande #{$old['order_number']}</h2>";
            $body .= "<p>Le client <b>{$senderName}</b> a mis à jour ses informations :</p>";
            $body .= "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            $body .= "<tr style='background: #eee;'><th>Champ</th><th>Ancienne valeur</th><th>Nouvelle valeur</th></tr>";
            $body .= "<tr><td><b>Adresse</b></td><td>{$old['delivery_address']}</td><td><b>{$_POST['delivery_address']}</b></td></tr>";
            $body .= "<tr><td><b>Date</b></td><td>{$old['delivery_date']}</td><td><b>{$_POST['delivery_date']}</b></td></tr>";
            $body .= "<tr><td><b>Heure</b></td><td>{$old['delivery_time']}</td><td><b>{$_POST['delivery_time']}</b></td></tr>";

            $oldEquip = $old['equipment_ready'] ? 'Prêt' : 'Non requis';
            $newEquip = $_POST['equipment_ready'] ? 'Prêt' : 'Non requis';
            $body .= "<tr><td><b>Matériel</b></td><td>$oldEquip</td><td><b>$newEquip</b></td></tr>";
            $body .= "</table>";

            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
        }

        header('Location: ../orders.php?msg=updated');
    } else {
        header('Location: ../orders.php?error=failed');
    }
    exit;
}
