<?php
// admin/get_user_tickets.php
session_start();
require_once '../php/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = getDBConnection();

    // Busco bilhetes ordenados por data
    $sql = "
        SELECT t.id, t.ticket_code, t.price, e.title as event_title, e.event_date, e.location
        FROM tickets t
        JOIN purchases p ON t.purchase_id = p.id
        JOIN events e ON t.event_id = e.id
        WHERE p.user_id = ?
        ORDER BY e.event_date DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>