<?php
session_start();
$message = "";

// Se veio alguma mensagem da sessão (ex: registro concluído ou erro de login)
if (isset($_SESSION['register_response'])) {
    $response = $_SESSION['register_response'];
    $message = $response["message"];
    unset($_SESSION['register_response']); // limpa para não repetir
}

if (isset($_SESSION['login_error'])) {
    $message = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card shadow-lg p-4 rounded-4">
                    <h3 class="text-center mb-4">Login</h3>

                    <!-- Mensagem de sucesso ou erro -->
                    <?php if ($message): ?>
                        <div class="alert alert-info text-center">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulário de login -->
                    <form action="process_login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nome de Utilizador</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <p class="text-center mt-3">
                        Não tem conta? <a href="register.php">Registre-se</a>
                    </p>
                    <p class="text-center">
                        <a href="indexpag.php">Voltar à página incial</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</body>

</html>