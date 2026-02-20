<?php

/**
 * Traitement du choix des cookies
 */

// On active le tampon de sortie pour éviter l'erreur "Headers already sent"
ob_start();

// On récupère le choix (accepted ou refused)
$choice = $_GET['choice'] ?? 'refused';

// Configuration commune des cookies pour PHP 8.2
$cookieOptions = [
    'path' => '/',
    'domain' => '', // On utilise une chaîne vide au lieu de null pour éviter le message "Deprecated"
    'secure' => isset($_SERVER['HTTPS']), // True si on est en HTTPS (Heroku l'est)
    'httponly' => true,
    'samesite' => 'Lax'
];

if ($choice === 'accepted') {
    // Cookie de 30 jours
    $cookieOptions['expires'] = time() + (3600 * 24 * 30);
    setcookie('cookie_consent', 'accepted', $cookieOptions);
} else {
    // Cookie de 24h
    $cookieOptions['expires'] = time() + (3600 * 24);
    setcookie('cookie_consent', 'refused', $cookieOptions);
}

// Redirection vers la page d'où venait l'utilisateur
$redirect = $_SERVER['HTTP_REFERER'] ?? '/index.php';

// On vide le tampon et on redirige
header('Location: ' . $redirect);
exit();
