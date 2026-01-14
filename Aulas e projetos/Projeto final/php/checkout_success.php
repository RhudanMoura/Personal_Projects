<?php
// php/checkout_success.php
session_start();

// Segurança: Só entra quem está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Concluída</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">
</head>

<body>

    <?php $root_path = '../';
    include '../components/navbar.php'; ?>

    <div class="container mt-5 text-center">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body p-5">

                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>

                <h2 class="fw-bold mb-3">Pagamento Confirmado!</h2>
                <p class="lead text-muted">
                    Obrigado pela sua compra, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>.
                </p>
                <p class="text-muted mb-4">
                    Os seus bilhetes foram gerados. Pode vê-los na sua área de cliente.
                </p>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="profile.php" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="bi bi-ticket-perforated"></i> Ver Meus Bilhetes
                    </a>
                    <a href="../index.php" class="btn btn-outline-secondary btn-lg px-4">
                        Voltar à Loja
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>
</body>

</html>