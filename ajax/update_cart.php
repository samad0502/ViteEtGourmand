<?php
session_start();
if (isset($_POST['index']) && isset($_POST['quantity'])) {
    $index = (int)$_POST['index'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $_SESSION['cart'][$index]['number_people'] = $quantity;
    }
}
header('Location: ../panier.php');
exit;
