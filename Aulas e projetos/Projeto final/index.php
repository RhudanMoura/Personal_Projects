<?php
// index.php (PÁGINA INICIAL)
// Olá! Eu sou a porta de entrada do site.
// Eu trato do Login, Logout e mostro os destaques na página inicial.

session_start();
require_once 'php/database.php';

// Variável para controlar qual alerta (SweetAlert) mostrar no final.
$login_status = ''; 

// =========================================================
// 1. LÓGICA DE LOGIN
// =========================================================
// Se recebi um formulário via POST com a ação "login"...
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    try {
        $pdo = getDBConnection();
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Eu procuro o utilizador pelo email.
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password_hash, user_type, is_active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Conta inativa? Bloqueio e aviso.
            if ($user['is_active'] == 0) {
                $login_status = 'deactivated';
            } 
            // Senha correta? Deixo entrar.
            elseif (password_verify($password, $user['password_hash'])) {
                // Sucesso! Guardo os dados essenciais na sessão.
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['user_email'] = $user['email'];
                
                // Recarrego a página para o menu atualizar (de "Entrar" para "Olá, Nome").
                header("Location: index.php");
                exit;
            } else {
                $login_status = 'error'; // Senha errada
            }
        } else {
            $login_status = 'error'; // Email não existe
        }
    } catch (PDOException $e) {
        $login_status = 'system_error';
    }
}

// =========================================================
// 2. LÓGICA DE LOGOUT
// =========================================================
// Se alguém clicou em "Sair"...
if (isset($_GET['logout'])) {
    session_destroy(); // Apago tudo da sessão.
    header("Location: index.php"); // Mando de volta para o início limpo.
    exit;
}

// =========================================================
// 3. CARREGAR EVENTOS EM DESTAQUE
// =========================================================
// Eu busco os próximos 3 eventos ativos para preencher o Carrossel e os Cards.
try {
    if (!isset($pdo)) $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 AND event_date >= NOW() ORDER BY event_date ASC LIMIT 3");
    $upcoming_events = $stmt->fetchAll();
} catch (PDOException $e) {
    $upcoming_events = []; // Se der erro, mostro uma lista vazia.
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eventos & Bilhetes</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="index.css" />

    <style>
    /* Estilos Gerais de Tipografia */
    body {
        font-family: 'Open Sans', sans-serif;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .navbar-brand,
    .btn {
        font-family: 'Poppins', sans-serif;
    }

    /* Estilos do Carrossel */
    .carousel-item {
        height: 600px;
    }

    .carousel-item img {
        height: 100%;
        object-fit: cover;
        filter: brightness(0.6);
        /* Escureço a imagem para o texto ler-se melhor */
    }

    /* Caixa de Texto Flutuante no Carrossel */
    .carousel-caption {
        bottom: 20%;
        background: rgba(255, 255, 255, 0.1);
        /* Fundo vidro */
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        max-width: 800px;
        left: 50%;
        transform: translateX(-50%);
        /* Centraliza perfeitamente */
    }

    /* Seção de Parceiros (Fundo Cinza) */
    .partner-section {
        background-color: #f8f9fa;
        padding: 80px 0 40px;
    }

    .cta-button-container {
        margin-top: 50px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>

    <?php $root_path = ''; include 'components/navbar.php'; ?>

    <?php if (!empty($upcoming_events)): ?>
    <div id="eventCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">

        <div class="carousel-indicators">
            <?php foreach($upcoming_events as $index => $slide): ?>
            <button type="button" data-bs-target="#eventCarousel" data-bs-slide-to="<?= $index ?>"
                class="<?= $index === 0 ? 'active' : '' ?>"></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach($upcoming_events as $index => $slide): 
                // Preparo a imagem e a data formatada
                $imgUrl = !empty($slide['image_url']) ? trim($slide['image_url']) : "https://images.unsplash.com/photo-1459749411177-0473ef71607b?auto=format&fit=crop&w=1600&q=80";
                $dateStr = date('d \d\e F \à\s H:i', strtotime($slide['event_date']));
            ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($imgUrl) ?>" class="d-block w-100"
                    alt="<?= htmlspecialchars($slide['title']) ?>">
                <div class="carousel-caption d-block">
                    <span class="badge bg-primary rounded-pill mb-3 px-3 py-2">
                        <i class="bi bi-calendar-event me-2"></i> <?= $dateStr ?>
                    </span>
                    <h2 class="display-4 fw-bold"><?= htmlspecialchars($slide['title']) ?></h2>
                    <p class="lead mb-4">
                        <?= htmlspecialchars($slide['short_description'] ?? substr($slide['description'], 0, 100)) ?>
                    </p>
                    <a href="php/events.php?q=<?= urlencode($slide['title']) ?>"
                        class="btn btn-light btn-lg px-5 rounded-pill shadow fw-bold text-primary">Comprar Bilhetes</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php else: ?>
    <div class="bg-dark text-white py-5 text-center">
        <h2>Em breve novos eventos!</h2>
    </div>
    <?php endif; ?>

    <div class="container mt-5 mb-5">
        <div class="row section-header text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold display-6">Destaques da Temporada</h2>
                <div class="d-flex justify-content-center mt-3">
                    <div style="width: 50px; height: 3px; background-color: #0d6efd;"></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="col-md-4">
                <div class="card event-card h-100 shadow-sm border-0">
                    <?php 
                        // Preparo a imagem e os dados para o modal
                        $imgUrl = !empty($event['image_url']) ? trim($event['image_url']) : "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1350&q=80";
                        $eventForModal = $event;
                        $eventForModal['image_url'] = $imgUrl;
                    ?>

                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($event['title']) ?>" style="height: 220px; object-fit: cover;">
                        <div
                            class="position-absolute top-0 start-0 bg-white px-3 py-2 m-3 rounded shadow-sm text-center">
                            <span class="d-block fw-bold text-primary" style="font-size: 1.2rem;">
                                <?= date('d', strtotime($event['event_date'])) ?>
                            </span>
                            <small class="d-block text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">
                                <?= date('M', strtotime($event['event_date'])) ?>
                            </small>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($event['title']) ?></h5>
                        <p class="card-text text-muted small mb-3">
                            <?= htmlspecialchars(substr($event['description'], 0, 90)) ?>...
                        </p>

                        <div class="mt-auto">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($event['location']) ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <span
                                    class="h5 text-primary mb-0 fw-bold">€<?= number_format($event['price'], 2, ',', '.') ?></span>
                                <button class="btn btn-outline-primary rounded-pill px-4 view-details"
                                    data-bs-toggle="modal" data-bs-target="#eventModal"
                                    data-event='<?= htmlspecialchars(json_encode($eventForModal), ENT_QUOTES, 'UTF-8') ?>'>
                                    Ver Detalhes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="php/events.php" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg fw-bold">Ver Todos os
                Eventos</a>
        </div>
    </div>

    <div id="secao-parceiros" class="partner-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form-container shadow-lg border-0 bg-white rounded-4 p-5">
                        <h3 class="text-center fw-bold mb-4">Fale Conosco</h3>

                        <form class="contact-form" id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nome *</label>
                                    <input type="text" class="form-control bg-light" id="firstName" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Apelido *</label>
                                    <input type="text" class="form-control bg-light" id="lastName" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Email *</label>
                                    <input type="email" class="form-control bg-light" id="email" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Telemóvel</label>
                                    <input type="tel" class="form-control bg-light" id="phone" maxlength="9"
                                        placeholder="Ex: 912345678"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Assunto *</label>
                                <select class="form-select bg-light" id="subject" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    <option value="parceria">Quero ser Parceiro</option>
                                    <option value="duvida">Dúvida sobre Bilhetes</option>
                                    <option value="problema">Problema Técnico</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Mensagem *</label>
                                <textarea class="form-control bg-light" id="message" rows="4" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-pill">Enviar
                                    Mensagem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="eventModalTitle">Detalhes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="eventModalBody"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cartConfirmationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title">Adicionado!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h5 id="confirmationEventTitle" class="fw-bold"></h5>
                    <p class="text-muted" id="confirmationDetails"></p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Continuar</button>
                    <a href="php/cart.php" class="btn btn-success">Ir ao Carrinho</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="config/api-config.js"></script>
    <script src="js/form-validation.js"></script>
    <script src="js/events.js"></script>

    <?php if ($login_status === 'deactivated'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => Swal.fire({
        title: 'Conta Desativada',
        text: 'Contacte o suporte.',
        icon: 'error'
    }));
    </script>
    <?php elseif ($login_status === 'error'): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => Swal.fire({
        title: 'Login Falhou',
        text: 'Dados incorretos.',
        icon: 'warning'
    }));
    </script>
    <?php endif; ?>

</body>

</html>