<?php

/**
 * Page de connexion
 */

session_start();

require '../classes/User.php';
include '../includes/header.php';
include '../includes/navbar.php';

$userClass = new User();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //  Nettoyage minimal
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $user = $userClass->login($email, $password);

    if ($user) {
        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'firstname' => $user['firstname'],
            'lastname'  => $user['lastname'],
            'email'     => $user['email'],
            'role_name'      => $user['role_name']
        ];

        // LOGIQUE DE REDIRECTION SELON LE RÔLE
        if ($user['role_name'] === 'admin') {
            header('Location: ../admin/admin_dashboard.php');
        } elseif ($user['role_name'] === 'employee') {
            header('Location: ../employee/employee_dashboard.php');
        } else {
            header('Location: ../menus.php');
        }
        exit;
    }

    $error = "Identifiants incorrects";
}
?>


<div class="container my-5">
    <h1>Connexion</h1>

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <?php
            switch ($_GET['msg']) {
                case 'check_email':
                    echo '<div class="alert-info p-3 rounded"><i class="bi bi-info-circle me-2"></i> Un lien de récupération a été envoyé sur votre boîte mail.</div>';
                    break;
                case 'password_updated':
                    echo '<div class="alert-success p-3 rounded"><i class="bi bi-check-circle me-2"></i> Votre mot de passe a été modifié avec succès. Connectez-vous !</div>';
                    break;
                case 'email_not_found':
                    echo '<div class="alert-danger p-3 rounded"><i class="bi bi-exclamation-triangle me-2"></i> Cet email n\'existe pas dans notre base de données.</div>';
                    break;
                case 'error':
                    echo '<div class="alert-danger p-3 rounded">Une erreur est survenue. Veuillez réessayer.</div>';
                    break;
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Mot de passe" required>
        <div class=" mt-3">
            <a href="forgot_password.php">Mot de passe oublié ?</a>
        </div>
        <br>
        <br><button class="btn btn-success">Se connecter</button>
    </form>
    <div>
        <br>
        Pas encore de compte ? <a href="./register.php">Créer un compte ici</a>
    </div>
</div>


<?php include '../includes/footer.php'; ?>