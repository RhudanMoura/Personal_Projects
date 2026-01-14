<?php
// components/navbar.php

// Inicializo o array para não dar erro se o banco falhar
$nav_categories = [];

// Lista de Ícones por ID (Igual ao events.php)
$category_icons = [
    1 => 'bi-music-note-beamed', // Música
    2 => 'bi-stars',             // Teatro
    3 => 'bi-trophy',            // Desporto
    4 => 'bi-palette',           // Arte
    5 => 'bi-mic',               // Conferências
    6 => 'bi-cup-hot',           // Gastronomia
    7 => 'bi-balloon',           // Festivais
    8 => 'bi-emoji-smile'        // Infantil
];

// Se a conexão com o banco ($pdo) existir nesta página, carrego as categorias ativas
if (isset($pdo)) {
    try {
        $nav_categories = $pdo->query("SELECT * FROM event_categories WHERE is_active = 1")->fetchAll();
    } catch (Exception $e) {
        // Se der erro, segue a vida sem categorias
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">

        <a class="navbar-brand" href="<?php echo $root_path; ?>index.php">
            <i class="bi bi-ticket-perforated"></i> Eventos & Bilhetes
        </a>

        <div class="d-flex align-items-center ms-auto d-lg-none">
            <a href="<?php echo $root_path; ?>php/cart.php" class="nav-cart-mobile me-3">
                <i class="bi bi-cart3 fs-3 text-white"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count"
                    style="font-size: 0.6rem;">0</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $root_path; ?>index.php">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $root_path; ?>php/events.php">Eventos</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categorias</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item fw-bold" href="<?php echo $root_path; ?>php/events.php">Ver
                                Todas</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <?php if (!empty($nav_categories)): ?>
                            <?php foreach ($nav_categories as $cat):
                                // Escolhe o ícone baseado no ID, ou usa 'bi-tag' se não estiver na lista
                                $icon = isset($category_icons[$cat['id']]) ? $category_icons[$cat['id']] : 'bi-tag';
                                ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?php echo $root_path; ?>php/events.php?cat=<?= $cat['id'] ?>">
                                        <i class="bi <?= $icon ?> me-2"></i> <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted">Sem categorias</span></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="<?php echo $root_path; ?>php/about.php">Sobre</a></li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item w-100">
                    <?php if (isset($_SESSION['user_id'])): ?>

                        <div class="dropdown text-center text-lg-start">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                Olá, <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $root_path; ?>php/profile.php">Minha Conta</a>
                                </li>

                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger fw-bold"
                                            href="<?php echo $root_path; ?>admin/dashboard.php">
                                            <i class="bi bi-speedometer2"></i> Painel Admin
                                        </a></li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo $root_path; ?>index.php?logout=true">
                                        <i class="bi bi-box-arrow-right"></i> Sair
                                    </a></li>
                            </ul>
                        </div>

                    <?php else: ?>

                        <form action="<?php echo $root_path; ?>index.php" method="POST"
                            class="d-flex align-items-center gap-2">
                            <input type="hidden" name="action" value="login">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Email"
                                required style="width: 150px;">
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Senha"
                                required style="width: 120px;">

                            <button type="submit" class="btn btn-primary btn-sm nav-login-btn">Entrar</button>
                            <a href="<?php echo $root_path; ?>php/register.php"
                                class="btn btn-success-custom btn-sm nav-login-btn">Registar</a>
                        </form>

                    <?php endif; ?>
                </li>

                <li class="nav-item nav-cart-desktop d-none d-lg-flex">
                    <a class="nav-link position-relative p-0" href="<?php echo $root_path; ?>php/cart.php"
                        onclick="<?php
                        // Se não estiver logado, bloqueio o clique e mostro alerta
                        if (!isset($_SESSION['user_id'])) {
                            echo "Swal.fire({ title: 'Login Necessário', text: 'Faça login para ver seu carrinho.', icon: 'warning', confirmButtonColor: '#0d6efd' }); return false;";
                        }
                        ?>">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count"
                            id="cart-count-desktop">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>