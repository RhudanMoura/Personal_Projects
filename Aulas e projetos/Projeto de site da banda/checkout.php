<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

function totalCart($cart)
{
    $sum = 0;
    foreach ($cart as $id => $item) {
        $sum += $item['price'] * $item['qty'];
    }
    return $sum;
}

// Variável para controlar se a compra foi finalizada
$compra_realizada = false;
$order_id = null;
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) $_SESSION['user_id'];
    $total = totalCart($cart);

    // 1) Cria o pedido
    $stmt = $conexao->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Pendente')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // 2) Insere itens
    $itemStmt = $conexao->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $pid => $item) {
        $pid = (int) $pid;
        $q = (int) $item['qty'];
        $pr = (float) $item['price'];
        $itemStmt->bind_param("iiid", $order_id, $pid, $q, $pr);
        $itemStmt->execute();
    }

    // 3) Limpa carrinho e marca compra como realizada
    $_SESSION['cart'] = [];
    $compra_realizada = true;
    $mensagem_sucesso = 'Compra realizada com sucesso! Número do pedido: ' . $order_id;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Checkout</h1>

        <!-- Exibe mensagem de sucesso se a compra foi realizada -->
        <?php if ($compra_realizada): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Sucesso!</strong> <?= htmlspecialchars($mensagem_sucesso) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="mt-3">
                <a href="indexpag.php" class="btn btn-primary">Voltar para a Loja</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$compra_realizada): ?>
        <!-- Mostra o formulário de checkout apenas se a compra ainda não foi feita -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Resumo do Pedido</h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($cart as $id => $item): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($item['name']) ?> x <?= (int) $item['qty'] ?></span>
                        <span>€<?= number_format($item['price'] * $item['qty'], 2, ',', '.') ?></span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>€<?= number_format(totalCart($cart), 2, ',', '.') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <form method="post">
            <button class="btn btn-success">Confirmar Pedido</button>
            <a href="cart.php" class="btn btn-outline-secondary">Voltar ao carrinho</a>
        </form>
        <?php endif; ?>
    </div>

    <!-- Script do Bootstrap para funcionar o alerta dismissible -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>