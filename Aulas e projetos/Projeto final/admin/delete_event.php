<?php
session_start();
require_once '../php/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    try {
        $pdo = getDBConnection();
        $id = $_GET['id'];

        // Apaga imagem
        $stmt = $pdo->prepare("SELECT image_url FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch();
        if ($event && !empty($event['image_url']) && file_exists('../' . $event['image_url'])) {
            unlink('../' . $event['image_url']);
        }

        // Apaga registo
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);
    } catch (Exception $e) {
    }
}
header("Location: dashboard.php");
exit;
?>