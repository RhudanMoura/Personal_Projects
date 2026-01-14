<?php
// admin/messages.php
// Olá! Eu sou a Caixa de Entrada (CRM) do sistema.
// Aqui eu mostro as mensagens de contacto e permito geri-las.

session_start();
require_once '../php/database.php';

// =========================================================
// 1. SEGURANÇA
// =========================================================
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$pdo = getDBConnection();

// =========================================================
// 2. PROCESSAMENTO DE AÇÕES (POST)
// Eu verifico se alguém clicou em "Marcar como Lida" ou "Apagar".
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = (int)$_POST['id'];
        
        if ($_POST['action'] === 'mark_read') {
            $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
        } elseif ($_POST['action'] === 'mark_unread') {
            $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?")->execute([$id]);
        } elseif ($_POST['action'] === 'delete') {
            $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
        }
    }
    // Faço um refresh na página para limpar o formulário e atualizar a lista.
    $params = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: messages.php" . $params);
    exit;
}

// =========================================================
// 3. BUSCA E FILTROS (GET)
// Eu monto a consulta SQL baseada no que o utilizador escolheu.
// =========================================================
$filter_status = $_GET['status'] ?? 'all';
$search_query  = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];

// Filtro por Estado (Lida/Não Lida)
if ($filter_status === 'unread') $sql .= " AND is_read = 0";
if ($filter_status === 'read')   $sql .= " AND is_read = 1";

// Filtro por Texto (Nome ou Email)
if (!empty($search_query)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY created_at DESC"; // As mais recentes primeiro

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* Estilos específicos para visualização mobile das mensagens */
    @media (max-width: 768px) {
        .mobile-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 0.85rem;
            display: block;
        }

        .mobile-value {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Borda colorida para identificar visualmente o estado no mobile */
        .card-msg {
            border-left: 5px solid #0d6efd;
            /* Azul = Nova */
        }

        .card-msg.read {
            border-left: 5px solid #6c757d;
            /* Cinza = Lida */
            opacity: 0.8;
        }
    }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="container mt-4 mb-5 flex-grow-1">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2"><i class="bi bi-envelope-open"></i> Caixa de Entrada</h1>
            <a href="dashboard.php" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <input type="text" name="q" class="form-control" placeholder="Buscar Nome ou Email..."
                            value="<?= htmlspecialchars($search_query) ?>">
                    </div>
                    <div class="col-6 col-md-auto">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>Todas</option>
                            <option value="unread" <?= $filter_status == 'unread' ? 'selected' : '' ?>>Não Lidas
                            </option>
                            <option value="read" <?= $filter_status == 'read' ? 'selected' : '' ?>>Lidas</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Estado</th>
                                <th>Data</th>
                                <th>Remetente</th>
                                <th>Assunto</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Nenhuma mensagem encontrada.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($messages as $msg): 
                                    $rowClass = $msg['is_read'] == 0 ? 'fw-bold bg-white' : 'text-muted bg-light';
                                ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="ps-4">
                                    <?php if ($msg['is_read'] == 0): ?>
                                    <span class="badge bg-danger">Nova</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Lida</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                                <td>
                                    <div><?= htmlspecialchars($msg['name']) ?></div>
                                    <small><?= htmlspecialchars($msg['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#msgModal<?= $msg['id'] ?>">Ler</button>

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('form-del-<?= $msg['id'] ?>')"><i
                                            class="bi bi-trash"></i></button>

                                    <form id="form-del-<?= $msg['id'] ?>" method="POST" style="display:none;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none p-3 bg-light">
                    <?php if (empty($messages)): ?>
                    <div class="text-center py-5 text-muted">Nenhuma mensagem encontrada.</div>
                    <?php else: ?>
                    <?php foreach ($messages as $msg): 
                            $cardClass = $msg['is_read'] == 0 ? 'card-msg bg-white' : 'card-msg read bg-light';
                            $statusLabel = $msg['is_read'] == 0 ? '<span class="badge bg-danger mb-2">Nova</span>' : '<span class="badge bg-secondary mb-2">Lida</span>';
                        ?>
                    <div class="card mb-3 shadow-sm <?= $cardClass ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <?= $statusLabel ?>
                                <small class="text-muted"><?= date('d/m H:i', strtotime($msg['created_at'])) ?></small>
                            </div>

                            <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($msg['subject']) ?></h5>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <span class="mobile-label">De:</span>
                                    <span class="mobile-value">
                                        <?= htmlspecialchars($msg['name']) ?> <br>
                                        <small class="text-muted"><?= htmlspecialchars($msg['email']) ?></small>
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-flex">
                                <button class="btn btn-primary flex-grow-1" data-bs-toggle="modal"
                                    data-bs-target="#msgModal<?= $msg['id'] ?>">
                                    <i class="bi bi-eye"></i> Ler Mensagem
                                </button>
                                <button class="btn btn-outline-danger"
                                    onclick="confirmDelete('form-del-mobile-<?= $msg['id'] ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <form id="form-del-mobile-<?= $msg['id'] ?>" method="POST" style="display:none;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <?php foreach ($messages as $msg): ?>
    <div class="modal fade" id="msgModal<?= $msg['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><?= htmlspecialchars($msg['subject']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border mb-3">
                        <div class="row">
                            <div class="col-md-6"><strong>De:</strong> <?= htmlspecialchars($msg['name']) ?></div>
                            <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></div>
                            <?php if($msg['phone']): ?>
                            <div class="col-md-6"><strong>Tel:</strong> <?= htmlspecialchars($msg['phone']) ?></div>
                            <?php endif; ?>
                            <div class="col-md-6"><strong>Data:</strong>
                                <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="p-3 bg-light rounded border" style="white-space: pre-line; word-wrap: break-word;">
                        <?= htmlspecialchars($msg['message']) ?>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                        <?php if($msg['is_read'] == 0): ?>
                        <input type="hidden" name="action" value="mark_read">
                        <button type="submit" class="btn btn-success"><i class="bi bi-check2-circle"></i> Marcar como
                            Lida</button>
                        <?php else: ?>
                        <input type="hidden" name="action" value="mark_unread">
                        <button type="submit" class="btn btn-outline-secondary">Marcar como Não Lida</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Tem a certeza?',
            text: "Esta mensagem será apagada permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, apagar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
    </script>
</body>

</html>