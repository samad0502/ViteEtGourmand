<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../classes/database.php';
require_once '../config/load_env.php';

$userRole = $_SESSION['user']['role'] ?? $_SESSION['user']['role_name'] ?? '';

if (!isset($_SESSION['user']) || ($userRole !== 'employee' && $userRole !== 'admin')) {
    die("Accès refusé");
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // ---  MODÉRATION D'UN AVIS ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['review_id'])) {
        $reviewId = (int)$_POST['review_id'];
        $redirect = ($userRole === 'admin') ? '../admin/admin_dashboard.php' : '../employee/employee_dashboard.php';

        if ($_POST['action'] === 'validate_review') {
            $stmt = $db->prepare("UPDATE reviews SET is_published = 1 WHERE id = ?");
            $stmt->execute([$reviewId]);
            header("Location: $redirect?msg=review_validated");
            exit;
        }

        if ($_POST['action'] === 'refuse_review') {
            $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$reviewId]);
            header("Location: $redirect?msg=review_refused");
            exit;
        }
    }

    // ---  MISE À JOUR DU STATUT COMMANDE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
        $orderId = (int)$_POST['order_id'];
        $newStatus = $_POST['new_status'];
        $redirect = ($userRole === 'admin') ? '../admin/admin_dashboard.php' : '../employee/employee_dashboard.php';

        // Mise à jour MySQL (On ajoute la réinitialisation de l'indicateur de modification)
        $stmt = $db->prepare("UPDATE orders SET order_status = ?, is_modified_by_client = 0 WHERE id = ?");
        $updateSuccess = $stmt->execute([$newStatus, $orderId]);

        if ($updateSuccess) {

            // --- PARTIE MONGODB : STATISTIQUES ---
            if ($newStatus === 'finished') {
                // On vérifie si l'extension système est chargée avant d'appeler la classe
                if (class_exists('\MongoDB\Driver\Manager')) {
                    $stmtInfo = $db->prepare("
            SELECT m.title, m.price, o.number_people 
            FROM orders o 
            JOIN menus m ON o.menu_id = m.id 
            WHERE o.id = ?
        ");
                    $stmtInfo->execute([$orderId]);
                    $orderData = $stmtInfo->fetch(PDO::FETCH_ASSOC);

                    if ($orderData) {
                        try {
                            $user = "prof_correction";
                            $pass = urlencode("ViteGourmand2026");
                            $cluster = "cluster0.ziybmvg.mongodb.net";
                            $uri = "mongodb+srv://$user:$pass@$cluster/?retryWrites=true&w=majority";

                            $manager = new \MongoDB\Driver\Manager($uri);
                            $bulk = new \MongoDB\Driver\BulkWrite;

                            $qty = ($orderData['number_people'] > 0) ? (int)$orderData['number_people'] : 1;

                            $bulk->insert([
                                'order_id'    => (int)$orderId,
                                'menu_name'   => $orderData['title'],
                                'price'       => (float)$orderData['price'],
                                'quantity'    => $qty,
                                'executed_at' => new \MongoDB\BSON\UTCDateTime()
                            ]);

                            $manager->executeBulkWrite('vite_gourmand.order_stats', $bulk);
                        } catch (Exception $e) {
                            // On log l'erreur sans bloquer l'utilisateur
                            error_log("Erreur NoSQL (Mongo) : " . $e->getMessage());
                        }
                    }
                } else {
                    // L'extension n'est pas encore active sur Heroku
                    error_log("L'extension ext-mongodb est absente. Les statistiques n'ont pas été enregistrées.");
                }
            }

            // --- PARTIE NOTIFICATION : EMAIL ---
            if ($newStatus === 'finished' || $newStatus === 'cancelled') {
                $stmtUser = $db->prepare("
                    SELECT u.email, u.firstname, o.order_number 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    WHERE o.id = ?
                ");
                $stmtUser->execute([$orderId]);
                $client = $stmtUser->fetch(PDO::FETCH_ASSOC);

                if ($client) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = getenv('MAIL_HOST');
                        $mail->SMTPAuth   = true;
                        $mail->Username   = getenv('MAIL_USER');
                        $mail->Password   = getenv('MAIL_PASS');
                        $mail->Port       = getenv('MAIL_PORT');
                        $mail->CharSet    = 'UTF-8';

                        $mail->setFrom('service-client@vite-gourmand.fr', 'Vite & Gourmand');
                        $mail->addAddress($client['email'], $client['firstname']);
                        $mail->isHTML(true);

                        if ($newStatus === 'finished') {
                            $mail->Subject = "Commande #" . $client['order_number'] . " prête !";
                            $mail->Body    = "<h2>Bonjour {$client['firstname']} !</h2><p>Votre commande est prête à être dégustée.</p>";
                        } else {
                            $mail->Subject = "Annulation commande #" . $client['order_number'];
                            $mail->Body    = "<h2>Bonjour {$client['firstname']},</h2><p>Nous sommes désolés, votre commande a été annulée.</p>";
                        }
                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Erreur Mailer : " . $mail->ErrorInfo);
                    }
                }
            }

            header("Location: $redirect?success=1");
            exit;
        } else {
            header("Location: $redirect?error=update_failed");
            exit;
        }
    }
} catch (Exception $e) {
    die("Erreur système : " . $e->getMessage());
}
