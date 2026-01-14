<?php
session_start();

// Se o usuário não está logado, volta para login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("conexao.php");

// Buscar informações do usuário logado
$user_id = $_SESSION['user_id'];
$sql = $conexao->prepare("SELECT username, email, user_type, profile_pic FROM users WHERE id = ?");
$sql->bind_param("i", $user_id);
$sql->execute();
$result = $sql->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <!-- Card centralizado -->
    <div class="card shadow-lg p-4 rounded-4 text-center" style="max-width: 400px; width: 100%;">
        <h2 class="mb-4">Perfil</h2>

        <!-- Foto -->
        <div class="d-flex justify-content-center mb-3">
            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Foto de perfil"
                class="img-fluid rounded-circle border shadow" style="width: 150px; height: 150px; object-fit: cover;">
        </div>

        <!-- Informações -->
        <p><strong>Nome de utilizador:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Tipo de utilizador:</strong> <?= htmlspecialchars($user['user_type']) ?></p>

        <!-- Botões -->
        <div class="d-flex flex-column gap-2 mt-4">
            <!-- "Sair" com fundo azul (vai para index.php mas mantém sessão) -->
            <a href="index.php" class="btn btn-primary">Página Inicial</a>

            <!-- "Logout" sem fundo (encerra a sessão) -->
            <a href="logout.php" class="btn btn-link text-danger">Logout</a>
        </div>
    </div>

</body>

</html>