<?php
// php/process_contact.php
session_start();
require_once 'database.php';

// Respondo sempre em JSON
header('Content-Type: application/json');

// Só aceito POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

// Recebo e limpo os dados
$first_name = trim($_POST['firstName'] ?? '');
$last_name = trim($_POST['lastName'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Junto o nome completo
$full_name = $first_name . ' ' . $last_name;

// Validação Básica
if (empty($first_name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Preencha os campos obrigatórios.']);
    exit;
}

try {
    $pdo = getDBConnection();

    // Gravo na tabela de mensagens
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$full_name, $email, $phone, $subject, $message]);

    echo json_encode(['success' => true, 'message' => 'Enviado com sucesso!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro técnico ao salvar.']);
}
?>