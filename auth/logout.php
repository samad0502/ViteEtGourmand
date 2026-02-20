<?php
session_start();

// Détruit toutes les données de session
$_SESSION = array();

// Détruit le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

// Redirige vers le login
header("Location: login.php");
exit();
