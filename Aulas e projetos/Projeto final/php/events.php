<?php
// PHP/events.php
session_start();
require_once 'database.php';

try {
    $pdo = getDBConnection();

    // 1. CAPTURAR OS FILTROS
    $category_filter = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
    $price_filter    = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 1000;
    $sort_filter     = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';
    $search_query    = isset($_GET['q']) ? trim($_GET['q']) : '';

    // 2. CONSULTA SQL
    $sql = "SELECT e.*, ec.name as category_name 
            FROM events e 
            LEFT JOIN event_categories ec ON e.category_id = ec.id 
            WHERE e.is_active = 1 AND e.event_date >= NOW()";
    
    $params = [];

    if ($category_filter) {
        $sql .= " AND e.category_id = ?";
        $params[] = $category_filter;
    }
    if ($price_filter) {
        $sql .= " AND e.price <= ?";
        $params[] = $price_filter;
    }
    if ($search_query) {
        $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }

    switch ($sort_filter) {
        case 'name_asc':  $sql .= " ORDER BY e.title ASC"; break;
        case 'name_desc': $sql .= " ORDER BY e.title DESC"; break;
        case 'price_asc': $sql .= " ORDER BY e.price ASC"; break;
        case 'price_desc':$sql .= " ORDER BY e.price DESC"; break;
        case 'date_desc': $sql .= " ORDER BY e.event_date DESC"; break;
        case 'date_asc': 
        default:          $sql .= " ORDER BY e.event_date ASC"; break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();

    // 3. DADOS AUXILIARES
    $cats = $pdo->query("SELECT * FROM event_categories WHERE is_active = 1")->fetchAll();
    $maxPriceDb = $pdo->query("SELECT MAX(price) FROM events WHERE is_active = 1 AND event_date >= NOW()")->fetchColumn();
    $maxPriceLimit = $maxPriceDb ? ceil($maxPriceDb) : 100;

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

// 4. MAPA DE ÍCONES (ID da Categoria -> Classe do Ícone)
// Ajuste conforme os IDs do seu banco de dados
$category_icons = [
    1 => 'bi-music-note-beamed', // Música
    2 => 'bi-stars',             // Teatro (Showbiz)
    3 => 'bi-trophy',            // Desporto
    4 => 'bi-palette',           // Arte & Cultura
    5 => 'bi-mic',               // Conferências
    6 => 'bi-cup-hot',           // Gastronomia
    7 => 'bi-balloon',           // Festivais
    8 => 'bi-emoji-smile'        // Infantil
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - Sistema de Bilhetes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">

    <style>
    /* Estilos do Menu Lateral */
    .sidebar-filter {
        background-color: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        /* Sticky apenas no Desktop */
        position: sticky;
        top: 90px;
        max-height: calc(100vh - 110px);
        overflow-y: auto;
        z-index: 99;
    }

    .sidebar-filter::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-filter::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .sidebar-filter::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .filter-title {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f2f5;
    }

    .category-link {
        display: block;
        padding: 8px 12px;
        color: #555;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        margin-bottom: 2px;
    }

    .category-link:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        transform: translateX(5px);
    }

    .category-link.active {
        background-color: #e7f1ff;
        color: #0d6efd;
        font-weight: 600;
    }

    input[type=range] {
        accent-color: #0d6efd;
        width: 100%;
    }

    .price-label {
        font-weight: bold;
        color: #0d6efd;
    }

    .qty-input {
        border-left: 0;
        border-right: 0;
        background-color: white !important;
    }

    /* --- CORREÇÃO MOBILE --- */
    @media (max-width: 991px) {
        .sidebar-filter {
            position: static;
            /* Remove o sticky que trava a tela */
            max-height: none;
            /* Deixa crescer naturalmente */
            overflow-y: visible;
            margin-bottom: 20px;
        }
    }
    </style>
</head>

<body>
    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row">

            <div class="col-lg-3 mb-4">

                <button class="btn btn-primary w-100 d-lg-none mb-3 shadow-sm fw-bold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false"
                    aria-controls="filterCollapse">
                    <i class="bi bi-funnel-fill me-2"></i> Filtrar Eventos
                </button>

                <div class="collapse d-lg-block" id="filterCollapse">
                    <div class="sidebar-filter">
                        <form action="" method="GET" id="filterForm">
                            <div class="mb-4">
                                <h6 class="filter-title">Buscar</h6>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="q" class="form-control border-start-0"
                                        placeholder="Nome do evento..." value="<?= htmlspecialchars($search_query) ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="filter-title">Categorias</h6>
                                <div class="d-flex flex-column">
                                    <a href="events.php"
                                        class="category-link <?= $category_filter === null ? 'active' : '' ?>">
                                        <i class="bi bi-grid-fill me-2"></i> Todos os Eventos
                                    </a>
                                    <?php foreach($cats as $c): 
                                        // Escolhe o ícone baseado no ID, ou usa um padrão se não existir
                                        $iconClass = isset($category_icons[$c['id']]) ? $category_icons[$c['id']] : 'bi-circle';
                                    ?>
                                    <a href="?cat=<?= $c['id'] ?>"
                                        class="category-link <?= $category_filter == $c['id'] ? 'active' : '' ?>">
                                        <i class="bi <?= $iconClass ?> me-2"></i> <?= htmlspecialchars($c['name']) ?>
                                    </a>
                                    <?php endforeach; ?>

                                    <?php if($category_filter): ?>
                                    <input type="hidden" name="cat" value="<?= $category_filter ?>">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="filter-title">Ordenar Por</h6>
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="date_asc" <?= $sort_filter == 'date_asc' ? 'selected' : '' ?>>Data:
                                        Mais Próxima</option>
                                    <option value="date_desc" <?= $sort_filter == 'date_desc' ? 'selected' : '' ?>>Data:
                                        Mais Distante</option>
                                    <option value="price_asc" <?= $sort_filter == 'price_asc' ? 'selected' : '' ?>>
                                        Preço: Menor primeiro</option>
                                    <option value="price_desc" <?= $sort_filter == 'price_desc' ? 'selected' : '' ?>>
                                        Preço: Maior primeiro</option>
                                    <option value="name_asc" <?= $sort_filter == 'name_asc' ? 'selected' : '' ?>>Nome:
                                        A-Z</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <h6 class="filter-title">
                                    Preço Máximo: <span class="price-label">€<span
                                            id="priceVal"><?= $price_filter ?></span></span>
                                </h6>
                                <input type="range" name="max_price" class="form-range" min="0"
                                    max="<?= $maxPriceLimit ?>" step="5" value="<?= $price_filter ?>"
                                    oninput="document.getElementById('priceVal').innerText = this.value">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                <?php if(!empty($_GET)): ?>
                                <a href="events.php" class="btn btn-link text-decoration-none btn-sm mt-2">Limpar
                                    Filtros</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row mb-2 align-items-center">
                    <div class="col">
                        <h2 class="fw-bold mb-0">Eventos Disponíveis</h2>
                        <p class="text-muted mb-2">Mostrando <?= count($events) ?> resultados</p>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center py-2 px-3">
                            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                            <div>Para comprar ingressos, você precisa <strong>efetuar o login</strong>.</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (empty($events)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-calendar-x display-1 opacity-25"></i>
                            <h4 class="mt-3">Nenhum evento encontrado.</h4>
                            <a href="events.php" class="btn btn-outline-primary mt-2">Limpar busca</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($events as $event): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card event-card h-100 border-0 shadow-sm">
                            <div class="category-badge">
                                <span
                                    class="badge bg-primary shadow-sm"><?= htmlspecialchars($event['category_name']) ?></span>
                            </div>

                            <?php 
                                $imgUrl = "https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80";
                                if (!empty($event['image_url'])) {
                                    $dbImage = trim($event['image_url']);
                                    if (strpos($dbImage, 'http') === 0) {
                                        $imgUrl = $dbImage;
                                    } else {
                                        $imgUrl = '../' . $dbImage;
                                    }
                                }
                                $eventForModal = $event;
                                $eventForModal['image_url'] = $imgUrl;
                            ?>

                            <div class="position-relative overflow-hidden">
                                <img src="<?= htmlspecialchars($imgUrl) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($event['title']) ?>"
                                    style="height: 220px; object-fit: cover; width: 100%;">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($event['title']) ?></h5>

                                <p class="card-text text-muted small mb-3">
                                    <?= htmlspecialchars($event['short_description'] ?? substr($event['description'], 0, 80) . '...') ?>
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-2 text-muted small">
                                        <i class="bi bi-calendar-event me-2 text-primary"></i>
                                        <?= date('d/m/Y H:i', strtotime($event['event_date'])) ?>
                                    </div>
                                    <div class="d-flex align-items-center mb-3 text-muted small">
                                        <i class="bi bi-geo-alt me-2 text-primary"></i>
                                        <?= htmlspecialchars($event['location']) ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <span
                                        class="h4 text-primary mb-0 fw-bold price-tag">€<?= number_format($event['price'], 2) ?></span>

                                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 view-details"
                                        data-bs-toggle="modal" data-bs-target="#eventModal"
                                        data-event='<?= htmlspecialchars(json_encode($eventForModal), ENT_QUOTES, 'UTF-8') ?>'>
                                        Detalhes
                                    </button>
                                </div>

                                <form class="add-to-cart-form mt-3" data-event-id="<?= $event['id'] ?>">
                                    <div class="d-flex gap-2">
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="changeQty(this, -1)">-</button>
                                            <input type="text" name="quantity"
                                                class="form-control text-center qty-input p-0" value="1" readonly>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="changeQty(this, 1)">+</button>
                                        </div>

                                        <?php if (isset($_SESSION['user_id'])): ?>
                                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 rounded-end">
                                            <i class="bi bi-cart-plus"></i> Adicionar
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm flex-grow-1 rounded-end"
                                            disabled>
                                            <i class="bi bi-lock"></i> Login
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="eventModalTitle">Detalhes</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="eventModalBody"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i> Adicionado!</h5><button
                        type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h5 id="confirmationEventTitle" class="fw-bold mb-2"></h5>
                    <p class="text-muted mb-0" id="confirmationDetails"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4"><button type="button"
                        class="btn btn-light" data-bs-dismiss="modal">Continuar a Comprar</button><a href="cart.php"
                        class="btn btn-success px-4">Ir para o Carrinho</a></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>
    <script src="../js/events.js"></script>
    <?php include '../components/footer.php'; ?>

    <script>
    function changeQty(btn, change) {
        const input = btn.parentElement.querySelector('input[name="quantity"]');
        let current = parseInt(input.value);
        let newVal = current + change;
        if (newVal >= 1 && newVal <= 9) {
            input.value = newVal;
        }
    }
    </script>
</body>

</html>