<?php
// php/update_profile.php
session_start();
require_once 'database.php';

// 1. Segurança
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        $user_id = $_SESSION['user_id'];

        // 2. Recebo os dados do formulário
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $phone = trim($_POST['phone']);

        // Validação básica
        if (empty($first_name) || empty($last_name)) {
            header("Location: profile.php?status=error&msg=Nome obrigatório");
            exit;
        }

        // 3. Atualizo no banco
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $phone, $user_id]);

        // 4. Atualizo a sessão (para o nome mudar no menu imediatamente)
        $_SESSION['user_name'] = $first_name;

        // Volto para o perfil com sucesso
        header("Location: profile.php?status=success");
        exit;

    } catch (PDOException $e) {
        header("Location: profile.php?status=error");
        exit;
    }
} else {
    header("Location: profile.php");
    exit;
}
?>