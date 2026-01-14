<?php
session_start();
require_once '../php/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($id != $_SESSION['user_id']) { // Não apaga a si mesmo
        try {
            $pdo = getDBConnection();
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        } catch (Exception $e) {
        }
    }
}
header("Location: dashboard.php");
exit;
?>