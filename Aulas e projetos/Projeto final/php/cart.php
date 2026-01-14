<?php
// php/cart.php
session_start();
require_once 'database.php';

// 1. SEGURANÇA
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];

// 2. AÇÃO: ATUALIZAR QUANTIDADE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $cart_id = $_POST['cart_id'];
    $change = (int) $_POST['change'];

    $stmtQtd = $pdo->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?");
    $stmtQtd->execute([$cart_id, $user_id]);
    $current = $stmtQtd->fetchColumn();

    if ($current) {
        $new_qty = $current + $change;
        if ($new_qty > 0) {
            $stmtUpd = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmtUpd->execute([$new_qty, $cart_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

// 3. AÇÃO: REMOVER ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $item_id = $_POST['item_id'];
    $stmtDel = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmtDel->execute([$item_id, $user_id]);
    header("Location: cart.php");
    exit;
}

// 4. BUSCAR ITENS
$stmt = $pdo->prepare("
    SELECT 
        c.id as cart_id, 
        c.quantity, 
        e.title, 
        e.price, 
        e.image_url, 
        e.event_date,
        e.location
    FROM cart c 
    JOIN events e ON c.event_id = e.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// 5. CALCULAR TOTAL
$total = 0;
foreach ($cart_items as $item) {
    $total += ($item['price'] * $item['quantity']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">

    <style>
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .cart-img {
        height: 100%;
        object-fit: cover;
        min-height: 150px;
    }

    /* AJUSTES PARA TELEMÓVEL */
    @media (max-width: 768px) {

        /* Imagem em cima, ocupando largura total */
        .cart-img {
            height: 200px;
            /* Altura fixa para ficar bonito */
            width: 100%;
            border-radius: 12px 12px 0 0 !important;
            /* Arredonda só em cima */
        }

        /* Título do Evento maior */
        .card-title {
            font-size: 1.3rem !important;
            margin-bottom: 5px;
        }

        /* Preço destacado */
        .text-primary.fw-bold {
            font-size: 1.4rem !important;
        }

        /* Botões de quantidade maiores (fáceis de tocar) */
        .btn-link {
            font-size: 1.5rem !important;
            /* Ícones grandes */
            padding: 0 15px !important;
            text-decoration: none;
        }

        /* Número da quantidade */
        .mx-2.fw-bold {
            font-size: 1.2rem;
        }

        /* Botão "Remover" com mais espaço */
        .btn-remove-mobile {
            margin-top: 15px;
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px;
            border: 1px solid #dc3545;
            border-radius: 8px;
            color: #dc3545;
        }

        /* Card do Resumo (Total) */
        .card.bg-light {
            margin-top: 20px;
        }
    }
    </style>
</head>

<body class="bg-light">

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="container mt-4 mb-5 flex-grow-1">
        <h2 class="mb-4 fw-bold"><i class="bi bi-cart3"></i> Meu Carrinho</h2>

        <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="bi bi-basket2 display-1 text-muted opacity-50"></i>
            <p class="mt-3 lead text-muted">O seu carrinho está vazio.</p>
            <a href="events.php" class="btn btn-primary btn-lg rounded-pill mt-3 px-5">Ver Eventos</a>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): 
                        $img = !empty($item['image_url']) ? $item['image_url'] : "https://via.placeholder.com/150";
                        if (strpos($img, 'http') !== 0) { $img = '../' . $img; }
                    ?>
                <div class="card mb-4 shadow-sm border-0 rounded-3">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <img src="<?= htmlspecialchars($img) ?>" class="img-fluid rounded-start cart-img"
                                alt="Evento">
                        </div>

                        <div class="col-md-9">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between mb-2">
                                    <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($item['title']) ?>
                                    </h5>
                                    <span
                                        class="fw-bold text-primary fs-5">€<?= number_format($item['price'], 2, ',', '.') ?></span>
                                </div>

                                <p class="text-muted small mb-4">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($item['event_date'])) ?> <br class="d-md-none">
                                    <span class="d-none d-md-inline"> | </span>
                                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($item['location']) ?>
                                </p>

                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">

                                    <div
                                        class="d-flex align-items-center border rounded-pill px-3 py-1 mb-3 mb-md-0 bg-white">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <input type="hidden" name="change" value="-1">
                                            <button type="submit" class="btn btn-link text-dark p-0"
                                                <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>
                                                <i class="bi bi-dash-circle"></i>
                                            </button>
                                        </form>

                                        <span class="mx-3 fw-bold fs-5"><?= $item['quantity'] ?></span>

                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_quantity">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <input type="hidden" name="change" value="1">
                                            <button type="submit" class="btn btn-link text-dark p-0">
                                                <i class="bi bi-plus-circle-fill text-primary"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <form method="POST" class="w-100 w-md-auto">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="item_id" value="<?= $item['cart_id'] ?>">
                                        <button type="submit"
                                            class="btn btn-link text-danger text-decoration-none btn-remove-mobile p-0">
                                            <i class="bi bi-trash"></i> Remover
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 90px; z-index: 1;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumo do Pedido</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span>€<?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold">Total</span>
                            <span class="h4 fw-bold text-primary">€<?= number_format($total, 2, ',', '.') ?></span>
                        </div>

                        <form action="checkout.php" method="POST">
                            <button type="submit" class="btn btn-success w-100 btn-lg rounded-pill fw-bold shadow py-3">
                                Finalizar Compra <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <div class="text-center mt-3 small text-muted">
                            <i class="bi bi-shield-lock-fill text-success"></i> Pagamento 100% Seguro
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>
</body>

</html>