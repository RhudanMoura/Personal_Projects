<?php
session_start();

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
function totalCart($cart) {
    $sum = 0;
    foreach ($cart as $id => $item) {
        $sum += $item['price'] * $item['qty'];
    }
    return $sum;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Carrinho de Compras</h1>

        <?php if (empty($cart)): ?>
        <div class="alert alert-info">Seu carrinho está vazio.</div>
        <a href="indexpag.php" class="btn btn-dark">Voltar à loja</a>
        <?php else: ?>
        <form action="update_cart.php" method="post">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $id => $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" width="60" height="60"
                                        style="object-fit:cover;">
                                    <span><?= htmlspecialchars($item['name']) ?></span>
                                </div>
                            </td>
                            <td>€<?= number_format($item['price'], 2, ',', '.') ?></td>
                            <td style="max-width:120px;">
                                <input type="number" class="form-control" min="1" name="qty[<?= (int)$id ?>]"
                                    value="<?= (int)$item['qty'] ?>">
                            </td>
                            <td>€<?= number_format($item['price'] * $item['qty'], 2, ',', '.') ?></td>
                            <td>
                                <a href="remove_from_cart.php?id=<?= (int)$id ?>"
                                    class="btn btn-sm btn-outline-danger">Remover</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th colspan="2">€<?= number_format(totalCart($cart), 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex gap-2">
                <a href="indexpag.php" class="btn btn-outline-secondary">Continuar comprando</a>
                <button class="btn btn-primary" type="submit">Atualizar Quantidades</button>
                <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>