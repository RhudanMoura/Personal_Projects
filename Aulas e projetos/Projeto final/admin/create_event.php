<?php
// admin/create_event.php
// Olá! Eu sou o controlador de criação de eventos.
// A minha função é validar os dados, salvar a imagem e guardar tudo no banco de dados.

session_start();
require_once '../php/database.php';

// =========================================================
// 1. SEGURANÇA (O Porteiro)
// =========================================================
// Primeiro, eu verifico se quem está a tentar entrar tem a credencial de 'admin'.
// Se não tiver, eu expulso-o imediatamente para a página inicial.
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';

// =========================================================
// 2. MENSAGEM DE SUCESSO (O Pós-Redirecionamento)
// =========================================================
// Se eu encontrar 'status=success' na URL, significa que acabei de gravar um evento
// e redirecionei o utilizador para cá. Então, mostro o alerta verde.
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Evento criado com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
}

// =========================================================
// 3. PROCESSAMENTO DO FORMULÁRIO (O Trabalho Pesado)
// =========================================================
// Se o método for POST, significa que o admin clicou em "Salvar Evento".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // Eu recolho e limpo os dados enviados pelo formulário
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $price = $_POST['price'];
        $tickets = $_POST['tickets'];
        $category = $_POST['category'];
        $location = $_POST['location'];

        // LÓGICA DE UPLOAD DE IMAGEM
        // Eu verifico se uma imagem foi enviada e se não houve erros técnicos.
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../uploads/events/';
            
            // Se a pasta não existir, eu crio-a com permissões de escrita.
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // Eu gero um nome único para o ficheiro para evitar duplicados (ex: event_65a4b...jpg)
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = 'event_' . uniqid() . '.' . $extension;
            
            // Tento mover o ficheiro da pasta temporária para a pasta final.
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $new_name)) {
                $image_url = 'uploads/events/' . $new_name; // Caminho para salvar no banco
            }
        }

        // SALVAR NO BANCO DE DADOS
        // Preparo a instrução SQL para evitar injeção de código malicioso.
        $sql = "INSERT INTO events (title, description, event_date, price, available_tickets, category_id, location, image_url, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $date, $price, $tickets, $category, $location, $image_url]);

        // =========================================================
        // O TRUQUE ANTI-F5 (PRG Pattern)
        // =========================================================
        // Em vez de mostrar a mensagem agora, eu redireciono o navegador para esta mesma página (GET).
        // Isso limpa a memória do navegador. Se o utilizador der F5 agora, ele só recarrega a página limpa,
        // não reenvia o formulário nem duplica o evento.
        header("Location: create_event.php?status=success");
        exit; // Paro o script aqui para garantir o redirecionamento.

    } catch (Exception $e) {
        // Se algo der errado (erro SQL, etc), mostro o erro na tela.
        $message = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
    }
}

// 4. PREPARAÇÃO DA VISUALIZAÇÃO
// Preciso carregar as categorias para preencher o <select> do formulário.
try {
    if (!isset($pdo)) $pdo = getDBConnection();
    $cats = $pdo->query("SELECT * FROM event_categories WHERE is_active = 1")->fetchAll();
} catch (Exception $e) { $cats = []; }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">
    <style>
    /* Ajustes para Mobile */
    @media (max-width: 768px) {
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
    </style>
</head>

<body>
    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-success"></i> Criar Novo Evento</h4>
                    </div>
                    <div class="card-body p-4">
                        <?= $message ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Data e Hora *</label>
                                    <input type="datetime-local" name="date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Local *</label>
                                    <input type="text" name="location" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Preço (€) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Stock de Bilhetes *</label>
                                    <input type="number" name="tickets" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Categoria</label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach($cats as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição *</label>
                                <textarea name="description" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Imagem</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4">Cancelar</a>
                                <button type="submit" class="btn btn-success px-5 fw-bold">Salvar Evento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>