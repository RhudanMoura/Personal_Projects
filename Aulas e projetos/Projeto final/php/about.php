<?php
// php/about.php
session_start();

// Incluímos o banco para o menu funcionar (caso precise carregar categorias)
require_once 'database.php';
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    // Ignora erro se não conectar, para a página abrir na mesma
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sobre Nós - Eventos & Bilhetes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../index.css" />

    <style>
    /* HERO SECTION */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 100px 0;
    }

    .feature-icon {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 1rem;
    }

    /* CARDS DE VALORES */
    .value-card {
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        background: white;
        border-radius: 15px;
    }

    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* EQUIPA */
    .team-card {
        border: none;
        background: transparent;
        transition: transform 0.3s ease;
        padding: 20px;
    }

    .team-card:hover {
        transform: translateY(-10px);
    }

    .team-img-circle {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin: 0 auto 15px;
        display: block;
    }

    /* ESTATÍSTICAS */
    .stats-section {
        background: #f8f9fa;
        padding: 80px 0;
    }

    .display-4 {
        font-weight: 700;
    }
    </style>
</head>

<body>

    <?php $root_path = '../'; include '../components/navbar.php'; ?>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Sobre Nós</h1>
            <p class="lead mb-4">
                Conectamos pessoas a experiências memoráveis desde 2020
            </p>
            <a href="#nossa-historia" class="btn btn-light btn-lg shadow-sm fw-bold text-primary">Conheça Nossa
                História</a>
        </div>
    </section>

    <section id="nossa-historia" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Nossa História</h2>
                    <p class="lead mb-4">
                        Tudo começou com uma simples ideia: tornar a compra de bilhetes
                        para eventos uma experiência simples, segura e agradável para todos.
                    </p>
                    <p>
                        Fundada em 2020, a <strong>Eventos & Bilhetes</strong> nasceu da
                        paixão por conectar pessoas a experiências incríveis. Começamos
                        como uma pequena startup e hoje somos referência no mercado de
                        venda de bilhetes online.
                    </p>
                    <p>
                        Acreditamos que cada evento é uma oportunidade única de criar
                        memórias inesquecíveis, e estamos aqui para garantir que você
                        tenha acesso aos melhores eventos da sua cidade.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Nossa Equipe" class="img-fluid rounded shadow" />
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section text-center">
        <div class="container">
            <h2 class="fw-bold mb-5">Números que Contam Nossa História</h2>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-primary">50K+</div>
                    <p class="lead">Bilhetes Vendidos</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-primary">500+</div>
                    <p class="lead">Eventos Realizados</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-primary">25K+</div>
                    <p class="lead">Clientes Satisfeitos</p>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-primary">15+</div>
                    <p class="lead">Cidades Atendidas</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Nossos Valores</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card value-card h-100 text-center p-4">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h4>Segurança</h4>
                        <p class="text-muted">
                            Garantimos transações 100% seguras e protegidas para todos os nossos clientes.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card value-card h-100 text-center p-4">
                        <i class="bi bi-heart feature-icon"></i>
                        <h4>Paixão</h4>
                        <p class="text-muted">
                            Amamos o que fazemos e acreditamos no poder transformador dos eventos.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card value-card h-100 text-center p-4">
                        <i class="bi bi-lightbulb feature-icon"></i>
                        <h4>Inovação</h4>
                        <p class="text-muted">
                            Estamos sempre em busca de novas tecnologias para melhorar sua experiência.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Conheça Nossa Equipa</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card team-card text-center">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80"
                            class="team-img-circle" alt="João Silva" />
                        <h4>João Silva</h4>
                        <p class="text-primary fw-bold mb-1">CEO & Fundador</p>
                        <p class="text-muted small">
                            Visionário apaixonado por tecnologia e experiências memoráveis.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card team-card text-center">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80"
                            class="team-img-circle" alt="Maria Santos" />
                        <h4>Maria Santos</h4>
                        <p class="text-primary fw-bold mb-1">Diretora de Operações</p>
                        <p class="text-muted small">
                            Garante que cada evento seja uma experiência perfeita para todos.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card team-card text-center">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80"
                            class="team-img-circle" alt="Pedro Costa" />
                        <h4>Pedro Costa</h4>
                        <p class="text-primary fw-bold mb-1">CTO</p>
                        <p class="text-muted small">
                            Lidera nossa equipa de desenvolvimento com paixão por inovação.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center p-4">
                        <i class="bi bi-bullseye feature-icon"></i>
                        <h3>Nossa Missão</h3>
                        <p class="lead">
                            Conectar pessoas a experiências extraordinárias através de uma plataforma segura e
                            intuitiva.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center p-4">
                        <i class="bi bi-eye feature-icon"></i>
                        <h3>Nossa Visão</h3>
                        <p class="lead">
                            Ser a plataforma líder em venda de bilhetes em Portugal, reconhecida pela excelência.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../config/api-config.js"></script>
</body>

</html>