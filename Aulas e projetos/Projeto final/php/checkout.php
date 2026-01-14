<?php
// php/checkout.php
session_start();
require_once 'database.php';

// 1. SEGURANÇA BÁSICA
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Só aceito se vier via POST (clique do botão)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

try {
    $pdo = getDBConnection();
    $user_id = $_SESSION['user_id'];

    // 2. BUSCO DADOS DO USUÁRIO (Nome e Email)
    // Preciso disso para gravar na compra
    $stmtUser = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $user = $stmtUser->fetch();

    $full_name = $user['first_name'] . ' ' . $user['last_name'];
    $email = $user['email'];

    // 3. BUSCO ITENS DO CARRINHO
    $stmtCart = $pdo->prepare("SELECT event_id, quantity FROM cart WHERE user_id = ?");
    $stmtCart->execute([$user_id]);
    $cart_items = $stmtCart->fetchAll();

    if (empty($cart_items)) {
        header("Location: cart.php"); // Carrinho vazio não compra
        exit;
    }

    // 4. INICIO A TRANSAÇÃO (Tudo ou Nada)
    $pdo->beginTransaction();

    // Preparo a Procedure que criámos no banco (CreatePurchase)
    // Ela insere na tabela 'purchases', na tabela 'tickets' e abate o estoque em 'events'
    $stmtPurchase = $pdo->prepare("CALL CreatePurchase(?, ?, ?, ?, ?)");

    $createdPurchaseIds = []; // Vou guardar os IDs gerados aqui

    foreach ($cart_items as $item) {
        $stmtPurchase->execute([
            $user_id,
            $item['event_id'],
            $item['quantity'],
            $full_name,
            $email
        ]);

        // Pego o ID da compra que a Procedure devolveu
        $result = $stmtPurchase->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['purchase_id'])) {
            $createdPurchaseIds[] = $result['purchase_id'];
        }

        $stmtPurchase->closeCursor(); // Libero para a próxima volta do loop
    }

    // 5. CONFIRMAR PAGAMENTO
    // Se criei compras, agora marco todas elas como 'paid' (Pago)
    if (!empty($createdPurchaseIds)) {
        // Crio string de interrogações: ?,?,?
        $placeholders = implode(',', array_fill(0, count($createdPurchaseIds), '?'));

        $sqlPay = "UPDATE purchases SET payment_status = 'paid', paid_at = NOW() WHERE id IN ($placeholders)";
        $stmtPay = $pdo->prepare($sqlPay);
        $stmtPay->execute($createdPurchaseIds);
    }

    // 6. LIMPAR CARRINHO
    // Como já comprei, esvazio o carrinho deste utilizador
    $stmtClear = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmtClear->execute([$user_id]);

    // 7. FINALIZAR
    $pdo->commit(); // Salvo tudo no banco definitivamente

    // Mando para a tela de sucesso
    header("Location: checkout_success.php");
    exit;

} catch (Exception $e) {
    // Se algo deu errado no meio do caminho, cancelo tudo (Rollback)
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erro ao processar compra: " . $e->getMessage());
}
?>