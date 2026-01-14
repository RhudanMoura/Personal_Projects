<?php
// PHP/get_cart_count.php
session_start();
require_once 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $pdo = getDBConnection();
    // Somo a quantidade de todos os itens do usuário
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();

    echo json_encode(['count' => $result['total'] ? (int) $result['total'] : 0]);

} catch (PDOException $e) {
    echo json_encode(['count' => 0]);
}
?>