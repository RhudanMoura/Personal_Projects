<?php
session_start();
include("conexao.php");

// Se não for post, volta
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Verificar se campos estão preenchidos
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Por favor, preencha todos os campos.";
    header("Location: login.php");
    exit;
}

// Buscar usuário no banco
$sql = $conexao->prepare("SELECT id, username, password_hash, user_type FROM users WHERE username = ?");
$sql->bind_param("s", $username);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verificar senha
    if (password_verify($password, $user['password_hash'])) {
        // Login válido → iniciar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Senha incorreta.";
    }
} else {
    $_SESSION['login_error'] = "Usuário não encontrado.";
}

// Se chegou aqui → login falhou
header("Location: login.php");
exit;
?>