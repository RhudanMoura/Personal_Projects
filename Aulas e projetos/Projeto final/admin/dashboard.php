<?php
// admin/dashboard.php
// Olá! Sou eu, o Painel de Controlo.
// Agora sim, a lógica está afinada: mostro primeiro os eventos que estão para acontecer (data mais próxima de hoje).

session_start();
require_once '../php/database.php'; 

// =========================================================
// 1. SEGURANÇA (O Porteiro)
// =========================================================
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

try {
    $pdo = getDBConnection();

    // =========================================================
    // 2. LÓGICA DE FILTROS
    // =========================================================
    // Eu verifico se existem filtros na URL
    $filter_category = isset($_GET['category']) ? $_GET['category'] : '';
    $filter_status   = isset($_GET['status']) ? $_GET['status'] : '';
    $filter_user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';

    $cats = $pdo->query("SELECT * FROM event_categories")->fetchAll();

    // =========================================================
    // 3. RECUPERAÇÃO DE DADOS (PHP)
    // =========================================================

    // A. Buscar Eventos
    $sqlEvents = "
        SELECT 
            e.*, 
            ec.name as category_name,
            (SELECT COUNT(*) FROM tickets t WHERE t.event_id = e.id) as sold_tickets 
        FROM events e 
        LEFT JOIN event_categories ec ON e.category_id = ec.id 
        WHERE 1=1
    ";

    $paramsEvents = [];

    // Filtro de Categoria
    if ($filter_category) {
        $sqlEvents .= " AND e.category_id = ?";
        $paramsEvents[] = $filter_category;
    }

    // Filtro de Status (Ativo/Encerrado)
    if ($filter_status === 'active') {
        $sqlEvents .= " AND e.is_active = 1 AND e.event_date >= NOW()";
    } elseif ($filter_status === 'ended') {
        $sqlEvents .= " AND e.event_date < NOW()";
    }

    // =========================================================
    // AQUI ESTÁ A CORREÇÃO DA ORDEM (Lógica "Próximos Eventos")
    // =========================================================
    // 1º Critério: (e.event_date >= NOW()) DESC 
    //    -> Isso cria dois grupos: Futuro (1) e Passado (0). 
    //    -> O DESC mete o Futuro no topo.
    // 2º Critério: e.event_date ASC
    //    -> Dentro do grupo Futuro, ordeno da data mais pequena (próxima) para a maior (distante).
    //    -> Resultado: O Concerto de Natal (20/Dez) aparece antes do Ano Novo (31/Dez).
    $sqlEvents .= " ORDER BY (e.event_date >= NOW()) DESC, e.event_date ASC";

    $stmtEvents = $pdo->prepare($sqlEvents);
    $stmtEvents->execute($paramsEvents);
    $events = $stmtEvents->fetchAll();

    // B. Buscar Utilizadores
    $sqlUsers = "SELECT * FROM users WHERE 1=1";
    $paramsUsers = [];

    if ($filter_user_type) {
        $sqlUsers .= " AND user_type = ?";
        $paramsUsers[] = $filter_user_type;
    }

    $sqlUsers .= " ORDER BY created_at DESC"; // Utilizadores mais recentes primeiro
    
    $stmtUsers = $pdo->prepare($sqlUsers);
    $stmtUsers->execute($paramsUsers);
    $users = $stmtUsers->fetchAll();

    // C. KPIs (Estatísticas)
    $total_vendas = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM purchases WHERE payment_status = 'paid'")->fetchColumn();
    $total_bilhetes = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn() ?: 0;
    $total_eventos_ativos = $pdo->query("SELECT COUNT(*) FROM events WHERE is_active = 1 AND event_date >= NOW()")->fetchColumn() ?: 0;
    $total_mensagens = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn() ?: 0;

} catch (PDOException $e) {
    $events = []; $users = []; 
    $total_vendas = 0; $total_bilhetes = 0; $total_eventos_ativos = 0; $total_mensagens = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* Deixo as linhas de eventos passados um pouco transparentes */
    .row-inactive {
        opacity: 0.6;
        background-color: #f8f9fa;
    }

    /* Ajustes para telemóveis */
    @media (max-width: 768px) {
        .mobile-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 0.9rem;
            display: block;
        }

        .mobile-value {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            margin-bottom: 10px;
        }
    }
    </style>
</head>

<body>

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="container mt-4 mb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2"><i class="bi bi-speedometer2"></i> Painel Admin</h1>
            <a href="create_event.php" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Novo Evento</span>
            </a>
        </div>

        <div class="row mb-5 g-3">
            <div class="col-6 col-lg-3">
                <div class="card text-white bg-primary mb-3 h-100 shadow-sm">
                    <div class="card-header fw-bold">Vendas</div>
                    <div class="card-body">
                        <h5 class="card-title fs-4">€ <?= number_format($total_vendas, 2, ',', '.') ?></h5>
                        <p class="mb-0 small">Confirmadas</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-white bg-success mb-3 h-100 shadow-sm">
                    <div class="card-header fw-bold">Bilhetes</div>
                    <div class="card-body">
                        <h5 class="card-title fs-4"><?= $total_bilhetes ?></h5>
                        <p class="mb-0 small">Emitidos</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-white bg-dark mb-3 h-100 shadow-sm">
                    <div class="card-header fw-bold">Eventos</div>
                    <div class="card-body">
                        <h5 class="card-title fs-4"><?= $total_eventos_ativos ?></h5>
                        <p class="mb-0 small">Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-dark bg-warning mb-3 h-100 shadow-sm position-relative">
                    <div class="card-header fw-bold">Msgs</div>
                    <div class="card-body">
                        <h5 class="card-title fs-4 d-flex align-items-center gap-2">
                            <?= $total_mensagens ?>
                            <?php if($total_mensagens > 0): ?><span class="badge bg-danger fs-6">!</span><?php endif; ?>
                        </h5>
                        <p class="mb-0 small">Por ler</p>
                        <a href="messages.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-5">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> Gestão de Eventos</h5>
                    </div>
                    <div class="col-md-8">
                        <form method="GET" class="d-flex gap-2 filter-form justify-content-md-end flex-wrap">
                            <select name="category" class="form-select form-select-sm" style="max-width: 150px;">
                                <option value="">Todas Categorias</option>
                                <?php foreach($cats as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $filter_category == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="status" class="form-select form-select-sm" style="max-width: 130px;">
                                <option value="">Todos Status</option>
                                <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Ativos
                                </option>
                                <option value="ended" <?= $filter_status === 'ended' ? 'selected' : '' ?>>Encerrados
                                </option>
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-filter"></i>
                                Filtrar</button>
                            <?php if($filter_category || $filter_status): ?>
                            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Limpar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Evento</th>
                            <th class="text-center">Status</th>
                            <th>Data</th>
                            <th>Preço</th>
                            <th class="text-center">Vendidos</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Nenhum evento encontrado com estes
                                filtros.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($events as $event): 
                            // Verifico se o evento já passou
                            $isEnded = strtotime($event['event_date']) < time();
                            // Se já passou, aplico a classe para ficar "cinzento"
                            $rowClass = $isEnded ? 'row-inactive' : '';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($event['title']) ?></div>
                                <small
                                    class="text-muted"><?= htmlspecialchars($event['category_name'] ?? 'Geral') ?></small>
                            </td>

                            <td class="text-center">
                                <?php if ($isEnded): ?>
                                <span class="badge bg-secondary">Encerrado</span>
                                <?php else: ?>
                                <span class="badge bg-success">Ativo</span>
                                <?php endif; ?>
                            </td>

                            <td><?= date('d/m/Y H:i', strtotime($event['event_date'])) ?></td>

                            <td>€ <?= number_format($event['price'], 2) ?></td>
                            <td class="text-center"><span
                                    class="badge bg-light text-dark border"><?= $event['sold_tickets'] ?></span></td>
                            <td class="text-center"><span
                                    class="badge bg-info text-dark"><?= $event['available_tickets'] ?></span></td>
                            <td class="text-end">
                                <a href="edit_event.php?id=<?= $event['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="openSecureDelete('event', <?= $event['id'] ?>)"><i
                                        class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-md-none p-3 bg-light">
                <?php foreach ($events as $event): 
                    $isEnded = strtotime($event['event_date']) < time();
                ?>
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-primary mb-0"><?= htmlspecialchars($event['title']) ?>
                            </h5>
                            <?php if ($isEnded): ?><span class="badge bg-secondary">Fim</span><?php else: ?><span
                                class="badge bg-success">On</span><?php endif; ?>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <span class="mobile-label">Data</span>
                                <span
                                    class="mobile-value"><?= date('d/m H:i', strtotime($event['event_date'])) ?></span>
                            </div>
                            <div class="col-6">
                                <span class="mobile-label">Preço</span>
                                <span class="mobile-value fw-bold">€ <?= number_format($event['price'], 2) ?></span>
                            </div>
                            <div class="col-6">
                                <span class="mobile-label">Vendidos</span>
                                <span class="mobile-value"><?= $event['sold_tickets'] ?></span>
                            </div>
                            <div class="col-6">
                                <span class="mobile-label">Stock</span>
                                <span class="mobile-value"><?= $event['available_tickets'] ?></span>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-flex">
                            <a href="edit_event.php?id=<?= $event['id'] ?>"
                                class="btn btn-outline-primary flex-grow-1"><i class="bi bi-pencil"></i> Editar</a>
                            <button class="btn btn-outline-danger flex-grow-1"
                                onclick="openSecureDelete('event', <?= $event['id'] ?>)"><i class="bi bi-trash"></i>
                                Apagar</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <h5 class="mb-0"><i class="bi bi-people me-2"></i> Utilizadores</h5>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex gap-2 justify-content-md-end">
                            <?php if($filter_category): ?><input type="hidden" name="category"
                                value="<?= $filter_category ?>"><?php endif; ?>
                            <?php if($filter_status): ?><input type="hidden" name="status"
                                value="<?= $filter_status ?>"><?php endif; ?>

                            <select name="user_type" class="form-select form-select-sm" style="max-width: 150px;">
                                <option value="">Todos Tipos</option>
                                <option value="client" <?= $filter_user_type === 'client' ? 'selected' : '' ?>>Clientes
                                </option>
                                <option value="admin" <?= $filter_user_type === 'admin' ? 'selected' : '' ?>>
                                    Administradores</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data Registo</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Nenhum utilizador encontrado.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php if ($u['user_type'] === 'admin'): ?>
                                <span class="badge bg-danger">ADMIN</span>
                                <?php else: ?>
                                <span class="badge bg-primary">Cliente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i
                                        class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="openSecureDelete('user', <?= $u['id'] ?>)"><i
                                        class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-md-none p-3 bg-light">
                <?php foreach ($users as $u): ?>
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0 fw-bold">
                                <?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></h5>
                            <?php if ($u['user_type'] === 'admin'): ?><span
                                class="badge bg-danger">ADMIN</span><?php else: ?><span
                                class="badge bg-primary">Cliente</span><?php endif; ?>
                        </div>
                        <p class="text-muted mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($u['email']) ?>
                        </p>
                        <p class="text-muted small mb-3"><i class="bi bi-calendar"></i> Registo:
                            <?= date('d/m/Y', strtotime($u['created_at'])) ?></p>

                        <div class="d-grid gap-2 d-flex">
                            <a href="edit_user.php?id=<?= $u['id'] ?>"
                                class="btn btn-outline-primary flex-grow-1">Editar</a>
                            <button class="btn btn-outline-danger flex-grow-1"
                                onclick="openSecureDelete('user', <?= $u['id'] ?>)">Apagar</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="securityModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-shield-lock-fill"></i> Segurança</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold text-danger">Ação irreversível!</p>
                    <p>Por favor, insira a sua senha de administrador:</p>

                    <form id="securityForm">
                        <input type="hidden" id="deleteType"> <input type="hidden" id="deleteId">
                        <div class="mb-3">
                            <label>Email Admin</label>
                            <input type="email" id="adminEmail" class="form-control"
                                value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Senha</label>
                            <input type="password" id="adminPass" class="form-control" placeholder="Sua senha">
                        </div>
                    </form>
                    <div id="securityError" class="text-danger fw-bold small mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>
    <script src="../js/admin-dashboard.js"></script>
</body>

</html>