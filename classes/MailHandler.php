<?php

require_once __DIR__ . '/../vendor/autoload.php';

// On utilise les "namespaces" pour que PHP sache où trouver PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHandler
{
    /**
     * Charge les variables d'environnement manuellement si on est en local
     */
    private static function loadEnvIfLocal()
    {
        $envPath = __DIR__ . '/../.env';
        if (file_exists($envPath)) {
            $env = parse_ini_file($envPath);
            foreach ($env as $key => $value) {
                putenv("$key=$value");
            }
        }
    }

    public static function sendOrderConfirmation($userEmail, $userName, $orderId, $total, $cartItems, $delivery_date)
    {
        self::loadEnvIfLocal(); // Chargement local si besoin
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP 
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($userEmail, $userName);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = "Confirmation de commande #$orderId - Vite Gourmand";

            // Formatage de la date  (ex: 15/02/2026)
            $dateFormatee = date('d/m/Y', strtotime($delivery_date));
            $montantFormate = number_format($total, 2, ',', ' ') . " €";

            $mail->Body = "<h1>Merci $userName !</h1>
               <p>Votre commande <strong>#$orderId</strong> de <strong>" . number_format($total, 2, ',', ' ') . " €</strong> est validée.</p>
               <p>Nous préparons vos menus pour le " . date('d/m/Y', strtotime($delivery_date)) . ".</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur mail : " . $mail->ErrorInfo);
            return false;
        }
    }


    public static function sendMaterialWarning($userEmail, $userName, $orderNumber)
    {
        self::loadEnvIfLocal();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($userEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = "IMPORTANT : Retour de matériel - Commande #$orderNumber";

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #dee2e6; padding: 20px;'>
                <h2 style='color: #dc3545;'>Rappel : Restitution de matériel</h2>
                <p>Bonjour <strong>$userName</strong>,</p>
                <p>Votre commande a bien été livrée avec le matériel de service prêté.</p>
                <p style='background-color: #fff3cd; padding: 15px; border-left: 5px solid #ffc107;'>
                    <strong>Attention :</strong> Conformément à nos CGV, vous disposez de <strong>10 jours ouvrés</strong> pour nous restituer ce matériel. 
                    Passé ce délai, une pénalité forfaitaire de <strong>600,00 €</strong> vous sera facturée.
                </p>
                <p>Merci de prendre contact avec notre société pour organiser le retour.</p>
                <br>
                <p>L'équipe Vite Gourmand</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendReviewRequest($userEmail, $userName)
    {
        self::loadEnvIfLocal();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($userEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = "Votre avis nous intéresse ! 🍽️";

            $reviewLink = "http://localhost/vite-gourmand/orders.php";

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee;'>
                <h2 style='color: #198754; text-align: center;'>Alors, c'était bon ?</h2>
                <p>Bonjour <strong>$userName</strong>,</p>
                <p>Votre commande est maintenant terminée. Nous espérons que vous vous êtes régalé !</p>
                <p>Pourriez-vous prendre une minute pour nous laisser une note et un petit commentaire ? Cela aide énormément nos chefs à s'améliorer.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$reviewLink' style='background-color: #198754; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                        Laisser mon avis
                    </a>
                </div>
                <p style='font-size: 0.9em; color: #666;'>À très bientôt pour une nouvelle commande !</p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur d'envoi de mail d'avis : " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendEmployeeWelcome($userEmail, $userName)
    {
        self::loadEnvIfLocal();
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($userEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = "Bienvenue dans l'équipe - Création de votre compte";

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2 style='color: #0d6efd;'>Bienvenue chez Vite Gourmand, $userName !</h2>
                <p>Nous avons le plaisir de vous informer qu'un compte <strong>Employé</strong> a été créé pour vous sur notre plateforme de gestion.</p>
                
                <p style='background-color: #f8f9fa; padding: 15px; border-left: 5px solid #0d6efd;'>
                    <strong>Identifiant de connexion :</strong> $userEmail
                </p>

                <p style='color: #dc3545; font-weight: bold;'>
                    Note importante concernant la sécurité :
                </p>
                <p>Conformément aux directives de la direction, votre mot de passe n'est pas communiqué dans cet email. 
                Veuillez vous rapprocher de <strong>José (Administrateur)</strong> pour obtenir vos accès provisoires en main propre.</p>

                <br>
                <p>À très bientôt dans nos cuisines,</p>
                <p><em>L'équipe administrative Vite Gourmand</em></p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi du mail de bienvenue employé : " . $mail->ErrorInfo);
            return false;
        }
    }
}
