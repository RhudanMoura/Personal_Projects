<?php
// php/profile.php
session_start();
require_once 'database.php';

// 1. SEGURANÇA: Só entro se estiver logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_data = null;
$my_tickets = [];

try {
    $pdo = getDBConnection();
    
    // 2. BUSCO OS MEUS DADOS
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    if (!$user_data) {
        // Se o utilizador não existe (ex: foi apagado), faço logout forçado
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    // 3. BUSCO OS MEUS BILHETES
    // Faço JOIN para trazer detalhes do evento e da compra
    $sqlTickets = "
        SELECT 
            t.ticket_code, t.price,
            e.title as event_title, e.event_date, e.location, e.image_url
        FROM tickets t
        JOIN purchases p ON t.purchase_id = p.id
        JOIN events e ON t.event_id = e.id
        WHERE p.user_id = ?
        ORDER BY e.event_date DESC
    ";
    $stmtTickets = $pdo->prepare($sqlTickets);
    $stmtTickets->execute([$_SESSION['user_id']]);
    $my_tickets = $stmtTickets->fetchAll();

} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body {
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: 40px;
    }

    .avatar-circle {
        width: 90px;
        height: 90px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 15px;
        border: 3px solid rgba(255, 255, 255, 0.5);
    }

    .ticket-item {
        border-left: 5px solid #667eea;
        transition: transform 0.2s;
    }

    .ticket-item:hover {
        transform: translateX(5px);
        background-color: #f8f9fa;
    }

    .main-content {
        flex: 1;
    }
    </style>
</head>

<body>

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <div class="main-content">
        <div class="profile-header text-center">
            <div class="container">
                <div class="avatar-circle"><i class="bi bi-person"></i></div>
                <h2 class="fw-bold"><?= htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']) ?>
                </h2>
                <p class="opacity-75"><?= htmlspecialchars($user_data['email']) ?></p>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-person-vcard text-primary me-2"></i> Meus Dados</h5>
                            <span class="badge bg-light text-dark border">Editável</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="update_profile.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nome</label>
                                        <input type="text" class="form-control" name="first_name"
                                            value="<?= htmlspecialchars($user_data['first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Apelido</label>
                                        <input type="text" class="form-control" name="last_name"
                                            value="<?= htmlspecialchars($user_data['last_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Email (Fixo)</label>
                                        <input type="email" class="form-control bg-light"
                                            value="<?= htmlspecialchars($user_data['email']) ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Telemóvel</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>"
                                            placeholder="Adicionar número">
                                    </div>
                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" class="btn btn-primary fw-bold px-4"><i
                                                class="bi bi-save me-2"></i> Salvar Alterações</button>
                                    </div>
                                </div>
                            </form>
                            <hr class="my-4">
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Membro desde:
                                <strong><?= date('d/m/Y', strtotime($user_data['created_at'])) ?></strong>
                            </p>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-ticket-perforated text-success me-2"></i> Meus Bilhetes
                            </h5>
                            <span class="badge bg-secondary"><?= count($my_tickets) ?></span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($my_tickets)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-ticket-detailed text-muted display-4 opacity-25"></i>
                                <p class="text-muted mt-3">Ainda não tem bilhetes.</p>
                                <a href="events.php" class="btn btn-primary btn-sm">Ver Eventos</a>
                            </div>
                            <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($my_tickets as $ticket): ?>
                                <div class="list-group-item ticket-item p-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <h5 class="mb-1 text-primary">
                                                <?= htmlspecialchars($ticket['event_title']) ?></h5>
                                            <div class="text-muted small mb-2">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($ticket['event_date'])) ?> |
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($ticket['location']) ?>
                                            </div>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-qr-code"></i>
                                                <?= htmlspecialchars($ticket['ticket_code']) ?></span>
                                        </div>
                                        <div class="text-end mt-2 mt-sm-0">
                                            <span
                                                class="d-block fw-bold text-success fs-5">€<?= number_format($ticket['price'], 2) ?></span>
                                            <button class="btn btn-sm btn-outline-primary mt-1"
                                                onclick="Swal.fire('Download', 'O PDF seria baixado aqui.', 'info')"><i
                                                    class="bi bi-download"></i> PDF</button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-5">
                        <a href="../index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                            Voltar</a>
                        <a href="../index.php?logout=true" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i>
                            Sair</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Perfil Atualizado!',
        text: 'Os seus dados foram salvos com sucesso.',
        timer: 3000,
        showConfirmButton: false
    });
    // Limpo a URL para o alerta não aparecer de novo se der F5
    window.history.replaceState(null, null, window.location.pathname);
    </script>
    <?php endif; ?>

</body>

</html>