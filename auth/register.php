<?php

/**
 * Page d'inscription
 */
ob_start();
session_start();

require_once __DIR__ . '/../classes/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../includes/header.php';
include '../includes/navbar.php';

$user = new User();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $data = [
        'firstname' => trim($_POST['firstname'] ?? ''),
        'lastname'  => trim($_POST['lastname'] ?? ''),
        'address'   => trim($_POST['address'] ?? ''),
        'city'      => trim($_POST['city'] ?? ''),
        'zip_code'  => trim($_POST['zip_code'] ?? ''),
        'phone'     => trim($_POST['phone'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'password'  => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? ''
    ];

    // ... (validations email et password identiques)

    if (empty($errors)) {
        try {
            // Inscription en BDD
            $user->register($data);

            // ENVOI DU MAIL
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            // On vérifie si les variables MAIL existent avant d'envoyer
            if (getenv('MAIL_HOST')) {
                try {
                    $mail->isSMTP();
                    $mail->Host       = getenv('MAIL_HOST');
                    $mail->SMTPAuth   = true;
                    $mail->Username   = getenv('MAIL_USER');
                    $mail->Password   = getenv('MAIL_PASS');
                    $mail->Port       = getenv('MAIL_PORT');
                    $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
                    $mail->addAddress($data['email'], $data['firstname']);

                    $mail->isHTML(true);
                    $mail->Subject = "Bienvenue chez Vite & Gourmand !";
                    $mail->Body = "<h2>Bienvenue " . htmlspecialchars($data['firstname']) . " !</h2>";
                    $mail->send();
                } catch (Exception $e) {
                    // On ne bloque pas l'inscription si le mail échoue
                }
            }

            // Connexion automatique
            $loggedUser = $user->login($data['email'], $data['password']);

            if ($loggedUser) {
                $_SESSION['user'] = [
                    'id'        => (int) $loggedUser['id'],
                    'firstname' => $loggedUser['firstname'],
                    'lastname'  => $loggedUser['lastname'],
                    'email'     => $loggedUser['email']
                ];
                header('Location: ../menus.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<div class="container my-5">
    <h1>Inscription</h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error) : ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="lastname" class="form-label">Nom</label>
        <input class="form-control mb-3" type="text" name="lastname" value="<?= htmlspecialchars($data['lastname'] ?? '') ?>" placeholder="Nom" required>

        <label for="firstname">Prénom</label>
        <input class="form-control mb-3" type="text" name="firstname" value="<?= htmlspecialchars($data['firstname'] ?? '') ?>" placeholder="Prénom" required>

        <label for="address">Adresse</label>
        <input class="form-control mb-3" type="text" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>" placeholder="Adresse" required>

        <label for="city">Ville</label>
        <input class="form-control mb-3" type="text" name="city" value="<?= htmlspecialchars($data['city'] ?? '') ?>" placeholder="Ville" required>

        <label for="zip_code">Code postal</label>
        <input class="form-control mb-3" type="text" name="zip_code" value="<?= htmlspecialchars($data['zip_code'] ?? '') ?>" placeholder="Code postal" required>

        <label for="phone">Téléphone</label>
        <input class="form-control mb-3" type="text" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" placeholder="Téléphone" required>

        <label for="email">Email</label>
        <input class="form-control mb-3" type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" placeholder="Email" required>

        <label for="password">Mot de passe</label>
        <input class="form-control mb-3" type="password" name="password" placeholder="Mot de passe" required>
        <small class="text-muted">
            Mot de passe : 10 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial.
        </small><br><br>

        <label for="password_confirm">Confirmer le mot de passe</label>
        <input class="form-control mb-3" type="password" name="password_confirm" placeholder="Confirmation du mot de passe" required>

        <br><button class="btn btn-success px-4 shadow-sm">S'inscrire</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>