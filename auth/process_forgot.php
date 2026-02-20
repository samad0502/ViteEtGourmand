<?php
require_once '../vendor/autoload.php';
require_once '../classes/database.php';
require_once '../config/load_env.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $db = (new Database())->getConnection();

    // Vérifier si l'utilisateur existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Sauvegarder dans la DB
        $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        // Envoyer l'email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username   = getenv('MAIL_USER');
            $mail->Password   = getenv('MAIL_PASS');
            $mail->Port       = getenv('MAIL_PORT');
            $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($data['email'], $data['firstname']);
            $mail->addAddress($email);
            $mail->Subject = "Reinitialisation de votre mot de passe";

            $link = "http://localhost/vite-gourmand/auth/reset_password.php?token=" . $token;
            $mail->isHTML(true);
            $mail->Body = "Cliquez ici pour changer votre mot de passe : <a href='$link'>$link</a>";

            $mail->send();
            header("Location: login.php?msg=check_email");
        } catch (Exception $e) {
            echo "Erreur : " . $mail->ErrorInfo;
        }
    } else {
        header("Location: login.php?msg=email_not_found");
    }
}
