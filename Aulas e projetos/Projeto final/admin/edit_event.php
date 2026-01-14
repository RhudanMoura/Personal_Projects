<?php
// admin/edit_event.php
session_start();
require_once '../php/database.php';

// 1. SEGURANÇA
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';
$event = null;

// 2. CARREGAR DADOS DO EVENTO
if (isset($_GET['id'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $event = $stmt->fetch();
        if (!$event)
            die("Evento não encontrado.");
    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit;
}

// 3. PROCESSAR ATUALIZAÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $price = $_POST['price'];
        $tickets = $_POST['tickets'];
        $category = $_POST['category'];
        $location = $_POST['location'];

        $image_url = $event['image_url']; // Começo com a imagem atual

        // Se enviou nova imagem
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../uploads/events/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = 'event_' . uniqid() . '.' . $extension;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $new_name)) {
                // Se deu certo, apago a antiga para poupar espaço
                if ($event['image_url'] && file_exists('../' . $event['image_url'])) {
                    unlink('../' . $event['image_url']);
                }
                $image_url = 'uploads/events/' . $new_name;
            }
        }

        $sql = "UPDATE events SET title=?, description=?, event_date=?, price=?, available_tickets=?, category_id=?, location=?, image_url=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $date, $price, $tickets, $category, $location, $image_url, $_GET['id']]);

        // Atualizo a variável local para mostrar na tela
        $event = array_merge($event, $_POST);
        $event['image_url'] = $image_url;

        $message = '<div class="alert alert-success">Evento atualizado! <a href="dashboard.php">Voltar</a></div>';

    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
    }
}

// 3. CARREGAR CATEGORIAS
try {
    if (!isset($pdo))
        $pdo = getDBConnection();
    $cats = $pdo->query("SELECT * FROM event_categories WHERE is_active = 1")->fetchAll();
} catch (Exception $e) {
    $cats = [];
}

$dateValue = date('Y-m-d\TH:i', strtotime($event['event_date']));
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* Ajustes EXCLUSIVOS para Mobile (não afeta Desktop) */
        @media (max-width: 768px) {

            .form-control,
            .form-select {
                font-size: 16px !important;
                /* Tamanho legível */
                padding: 12px;
                /* Área de toque maior */
            }

            .btn {
                width: 100%;
                /* Botões ocupam a largura toda no telemóvel */
                margin-bottom: 10px;
                padding: 12px;
                font-size: 1.1rem;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .d-flex {
                flex-direction: column-reverse;
                /* Botão Salvar em cima, Cancelar em baixo */
            }
        }
    </style>
</head>

<body>

    <?php $root_path = '../';
    include '../components/navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary"></i> Editar Evento</h4>
                    </div>
                    <div class="card-body p-4">
                        <?= $message ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?= htmlspecialchars($event['title']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Data</label>
                                    <input type="datetime-local" name="date" class="form-control"
                                        value="<?= $dateValue ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Local</label>
                                    <input type="text" name="location" class="form-control"
                                        value="<?= htmlspecialchars($event['location']) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Preço</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="<?= $event['price'] ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Stock</label>
                                    <input type="number" name="tickets" class="form-control"
                                        value="<?= $event['available_tickets'] ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Categoria</label>
                                    <select name="category" class="form-select" required>
                                        <?php foreach ($cats as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $event['category_id'] ? 'selected' : '' ?>><?= $c['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição</label>
                                <textarea name="description" class="form-control" rows="5"
                                    required><?= htmlspecialchars($event['description']) ?></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Imagem</label>
                                <?php if (!empty($event['image_url'])): ?>
                                    <div class="mb-2 p-2 border rounded bg-light">
                                        <img src="../<?= htmlspecialchars($event['image_url']) ?>"
                                            style="height: 100px; border-radius: 5px; object-fit: cover;">
                                        <span class="text-muted ms-2 small">Imagem Atual</span>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold">Salvar Alterações</button>
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