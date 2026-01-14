<?php
// add_to_cart.php
session_start();
include 'conexao.php';

// responder sempre JSON
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 1;
$qty = max(1, $qty);

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Produto inválido']);
    exit;
}

// Buscar produto no BD (prepared statement)
$stmt = $conexao->prepare("SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
    exit;
}

$prod = $res->fetch_assoc();

// Iniciar carrinho na sessão se não existir
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Garantir chave inteira
$pid = (int) $prod['id'];
if (!isset($_SESSION['cart'][$pid])) {
    $_SESSION['cart'][$pid] = [
        'id' => $pid,
        'name' => $prod['name'],
        'price' => (float) $prod['price'],
        'qty' => 0,
        'image' => $prod['image']
    ];
}

// Adicionar quantidade
$_SESSION['cart'][$pid]['qty'] += $qty;

// calcular quantidade total no carrinho (soma de qty)
$cartCount = 0;
foreach ($_SESSION['cart'] as $it) {
    $cartCount += (int) $it['qty'];
}

// Retorna o item adicionado e o count
echo json_encode([
    'success' => true,
    'item' => $_SESSION['cart'][$pid],
    'cartCount' => $cartCount
]);
exit;