<?php

/**
 * Retourne les thèmes et les régimes
 */

require_once '../classes/database.php';

$pdo = Database::getConnection();

// Récupération des thèmes
$themesStmt = $pdo->query("SELECT id, name FROM themes");
$themes = $themesStmt->fetchAll();

// Récupération des régimes
$dietsStmt = $pdo->query("SELECT id, name FROM diets");
$diets = $dietsStmt->fetchAll();

// Retour JSON
header('Content-Type: application/json');
echo json_encode([
    'themes' => $themes,
    'diets'  => $diets
]);
