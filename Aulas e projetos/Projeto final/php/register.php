<?php
// php/register.php
session_start();
require_once 'database.php'; 

$message = ''; 

// 1. Se enviou o formulário...
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Recebo os dados
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 3. Verifico senhas
    if ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger">As palavras-passe não coincidem!</div>';
    } else {
        try {
            $pdo = getDBConnection();

            // Verifico se email já existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-danger">Este email já está registado!</div>';
            } else {
                // Crio hash e salvo
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (first_name, last_name, email, password_hash, user_type) VALUES (?, ?, ?, ?, 'client')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$first_name, $last_name, $email, $password_hash]);

                // Sucesso
                $message = '<div class="alert alert-success">Conta criada! <a href="../index.php">Faça login na Home</a>.</div>';
            }

        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Erro no sistema.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">
    <style>
    body {
        background-color: #f8f9fa;
        display: block;
        min-height: 100vh;
    }

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .register-card {
        max-width: 500px;
        width: 100%;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        text-align: center;
        padding: 20px;
        border-radius: 15px 15px 0 0 !important;
    }
    </style>
</head>

<body>

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="register-container">
        <div class="card register-card">
            <div class="card-header">
                <h3><i class="bi bi-person-plus"></i> Criar Nova Conta</h3>
                <p class="mb-0">Preencha os dados para se registar</p>
            </div>

            <div class="card-body p-4">
                <?php if (!empty($message)) echo $message; ?>

                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apelido</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirmar Palavra-passe</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Registar Conta</button>
                        <a href="../index.php" class="btn btn-outline-secondary">Voltar ao Início</a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3 bg-white border-0 rounded-bottom">
                <small>Já tem conta? Faça login no menu acima.</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>