<?php
session_start();

if (!isset($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $q) {
        $id = (int) $id;
        $q = max(1, (int) $q);
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = $q;
        }
    }
}

header('Location: cart.php');
exit;