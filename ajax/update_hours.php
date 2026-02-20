<?php
session_start();
require_once __DIR__ . '/../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = (new Database())->getConnection();

        foreach ($_POST['open'] as $id => $openTime) {
            $closeTime = $_POST['close'][$id];
            // On vérifie si la checkbox "fermé" est cochée pour cet ID
            $isClosed = isset($_POST['closed'][$id]) ? 1 : 0;

            $stmt = $db->prepare("UPDATE opening_hours SET open_time = ?, close_time = ?, is_closed = ? WHERE id = ?");
            $stmt->execute([$openTime, $closeTime, $isClosed, $id]);
        }

        header("Location: ../employee/employee_dashboard.php?msg=hours_updated");
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}
