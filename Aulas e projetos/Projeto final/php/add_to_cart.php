<?php
// PHP/add_to_cart.php
session_start();
require_once 'database.php';

// Respondo sempre em JSON para o JavaScript
header('Content-Type: application/json');

// 1. SEGURANÇA: Login Obrigatório
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa fazer login para adicionar itens ao carrinho.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    $user_id = $_SESSION['user_id'];

    if (!$event_id || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }

    try {
        $pdo = getDBConnection();

        // 2. BUSCO O EVENTO (Para checar estoque)
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_active = 1");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'Evento não encontrado']);
            exit;
        }

        // 3. VERIFICAÇÃO DE ESTOQUE (STOCK REAL)
        // Se pedir 10 e só tiver 5, bloqueio aqui.
        if ($event['available_tickets'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Bilhetes insuficientes. Disponível: ' . $event['available_tickets']]);
            exit;
        }

        // 4. VERIFICO SE JÁ EXISTE NO CARRINHO
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Se já existe, somo a nova quantidade
            $new_quantity = $existing['quantity'] + $quantity;

            // Validação Extra: A soma total não pode estourar o estoque
            if ($new_quantity > $event['available_tickets']) {
                echo json_encode(['success' => false, 'message' => 'Não há stock suficiente para adicionar mais essa quantidade.']);
                exit;
            }

            // Atualizo
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $existing['id']]);
            $action = 'atualizado';

        } else {
            // Se não existe, crio novo
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, event_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $event_id, $quantity]);
            $action = 'adicionado';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Item ' . $action . ' ao carrinho com sucesso!'
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro técnico: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>