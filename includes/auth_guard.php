<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si la session n'existe pas, on redirige vers le login
if (!isset($_SESSION['user'])) {

    header("Location: ../auth/login.php");
    exit();
}
