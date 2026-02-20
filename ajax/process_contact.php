<?php
require_once '../vendor/autoload.php';
require_once '../config/load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $user_message = nl2br(htmlspecialchars($_POST['message']));

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('MAIL_USER');
        $mail->Password = getenv('MAIL_PASS');
        $mail->Port = getenv('MAIL_PORT');
        $mail->CharSet = 'UTF-8';

        // envoie du mail de l'utilisateur 
        $mail->setFrom($email, $name);

        //  reception de l'administrateur 
        $mail->addAddress('admin@vite-gourmand.fr', 'Admin Vite & Gourmand');

        // Contenu du mail
        $mail->isHTML(true);
        $mail->Subject = "Nouveau contact : $subject";
        $mail->Body = "
            <h3>Nouveau message de contact</h3>
            <p><strong>Nom :</strong> $name</p>
            <p><strong>Email :</strong> $email</p>
            <p><strong>Sujet :</strong> $subject</p>
            <hr>
            <p><strong>Message :</strong><br>$user_message</p>
        ";

        $mail->send();

        // Redirection avec succès
        header("Location: ../contact.php?status=success");
        exit;
    } catch (Exception $e) {
        header("Location: ../contact.php?status=error");
        exit;
    }
}
