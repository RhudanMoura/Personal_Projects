<?php
// admin/secure_delete.php
session_start();
require_once '../php/database.php';

header('Content-Type: application/json');

// 1. Apenas Admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// 2. Recebo o JSON do JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$admin_email = $input['admin_email'] ?? '';
$admin_pass = $input['admin_pass'] ?? '';
$type = $input['type'] ?? ''; // 'event' ou 'user'
$id = $input['id'] ?? 0;

try {
    $pdo = getDBConnection();

    // 3. RE-AUTENTICAÇÃO (Verifico se a senha está certa)
    $stmtAuth = $pdo->prepare("SELECT password_hash FROM users WHERE email = ? AND user_type = 'admin'");
    $stmtAuth->execute([$admin_email]);
    $admin = $stmtAuth->fetch();

    if (!$admin || !password_verify($admin_pass, $admin['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Senha de administrador incorreta!']);
        exit;
    }

    // 4. EXCLUSÃO
    if ($type === 'event') {
        // Apago a imagem primeiro
        $stmtImg = $pdo->prepare("SELECT image_url FROM events WHERE id = ?");
        $stmtImg->execute([$id]);
        $img = $stmtImg->fetch();
        if ($img && !empty($img['image_url']) && file_exists('../' . $img['image_url'])) {
            unlink('../' . $img['image_url']);
        }
        // Apago do banco
        $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$id]);

    } elseif ($type === 'user') {
        if ($id == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Você não pode se excluir!']);
            exit;
        }
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>