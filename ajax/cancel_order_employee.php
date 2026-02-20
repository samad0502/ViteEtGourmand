<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../config/load_env.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $reason = htmlspecialchars($_POST['reason']);
    $contactMethod = $_POST['contact_method'];

    try {
        $db = (new Database())->getConnection();

        // Mettre à jour le statut en 'cancelled'
        $stmt = $db->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ?");
        $stmt->execute([$orderId]);

        // Récupérer les infos du client
        $stmtUser = $db->prepare("
            SELECT u.email, u.firstname, o.order_number 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmtUser->execute([$orderId]);
        $client = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // Si le mode de contact est Email, on envoie le mail
        if ($client && $contactMethod === 'Email') {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($client['email'], $client['firstname']);
            $mail->isHTML(true);
            $mail->Subject = "Annulation de votre commande #" . $client['order_number'];
            $mail->Body    = "<h2>Bonjour {$client['firstname']},</h2>
                            <p>Nous vous informons que votre commande a été annulée pour le motif suivant :</p>
                            <p><em>$reason</em></p>
                            <p>Nous restons à votre disposition par téléphone.</p>";
            $mail->send();
        }

        header("Location: ../employee/employee_dashboard.php?msg=cancelled");
        exit;
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
