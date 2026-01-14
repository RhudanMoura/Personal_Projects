<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesindex.css?v=1" />
    <title>Portfólio</title>
    <meta name="description"
        content="Portifólio do Rhudan Moura. Desenvolvedor Front-end, back-end e UI-UX-Design. Crio sites responsivos e atrativos para pequenas e grandes empresas." />
    <meta name="keywords"
        content="Rhudan Moura, Desenvolvedor, Front-end, Back-end, UI-UX-Design, Portifólio, Responsivo, Pequenas empresas, Grandes empresas." />
    <meta name="Author" content="Rhudan Moura" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />

</head>

<body>


    <nav class="navbar bg-primary">
        <div class="container-fluid">

            <div class="position-absolute start-50 translate-middle-x text-center text-white d-none d-lg-block">
                <h4 class="mb-0">RHUDAN MOURA</h4>
                <strong class="text-light">Portfólio</strong>
            </div>

            <!-- Botões à direita -->
            <div class="d-flex align-items-start">
                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Ícone de usuário no lugar do login -->
                <a href="profile.php"
                    class="btn btn-primary text-white me-2 d-flex align-items-center justify-content-center"
                    style="width: 100px;">
                    <i class="fas fa-user"></i>
                </a>
                <?php else: ?>
                <a href="login.php" class="btn btn-primary text-white me-2">Login</a>
                <a href="register.php" class="btn btn-primary text-white me-2">Register</a>
                <a href="admin.php" class="btn btn-primary text-white">Admin</a>
                <?php endif; ?>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Aboutme.html">Sobre</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Trabalhos
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="formulario.html">Simulação</a>
                                </li>
                                <li><a class="dropdown-item" href="faq.html">FAQ</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a class="dropdown-item" href="contactme.html">Contactos</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-4">
                <img src="Imagens/PersonalPhoto.jpeg" alt="imagem1" class="img1" />
            </div>
            <div class="col-sm-8 mb-3 center-text">
                <h2>Olá, Sou Rhudan Moura e sou Web Developer</h2>
            </div>
        </div>
        <div class="mt-3">
            <h3>Sobre mim</h3>
            <p>
                Sou apaixonado por criar experiências digitais envolventes e
                intuitivas. Posso ajudar a sua ideia em realidade.
            </p>
            <a href="servicos.html">
                <button class="btn btn-primary" type="button">
                    Serviços oferecidos
                </button></a>
        </div>
        <h3 class="mt-3">As minhas skills</h3>
        <hr />
        <div class="row mt-3">
            <div class="col-sm-4 front">
                <h3>Desenvolvimento Front-end</h3>
                <p>
                    Domino HTML, CSS e JavaScript para criar interfaces rensponsivas e
                    interativas.
                </p>
                <img src="Imagens/FrontEndImage.jpg" alt="Front-end" class="img2" />
            </div>
            <div class="col-sm-4 front">
                <h3>Desenvolvimento Back-end</h3>
                <p>
                    Construo soluções robustas e seguras com Nodejs. PHP e base de
                    dados.
                </p>
                <img src="Imagens/BackEndImage.jpg" alt="back-end" class="img2" />
            </div>
            <div class="col-sm-4 front">
                <h3>Desenvolvimento UX-UI</h3>
                <p>
                    Penso no utilizador em cada etapa, projetando layouts limpos e
                    experiências intuitivas.
                </p>
                <img src="Imagens/UI-UX-Design.jpg" alt="UI/UX" class="img2" />
            </div>
        </div>
        <div class="about-me">
            <button class="btn btn-primary mt-3 btn" type="button">
                Saiba mais sobre mim
            </button>
        </div>

        <div id="carouselExampleDark" class="carousel carousel-dark slide mt-3">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <img src="Imagens/e-commerce.png" class="d-block w-100" alt="e-commerce-image" />
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Site de E-commerce</h5>
                        <p>
                            Criamos sites responsivos de E-commerce para grandes e pequenas
                            empresas.
                        </p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="2000">
                    <img src="Imagens/travelsite.jpg" class="d-block w-100" alt="traveling-image" />
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Site de viagem</h5>
                        <p>
                            Criamos site de viagens responsivos, bonitos e atraentes para
                            sua empresa atrair mais clientes.
                        </p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="Imagens/sitepessoal.png" class="d-block w-100" alt="personal-site-img." />
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Site pessoal</h5>
                        <p>
                            Criamos site pessoal para que você possa compartilhar suas
                            conquistas e seus trabalhos.
                        </p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="mt-3">
            <h3>Os meus projetos</h3>
            <hr />
            <p>
                <b>E-commerce:</b>Desenvolvimento de uma plataforma de e-commerce
                personalizável e otimizada para vendas online.
            </p>
            <p>
                <b>Aplicação móvel:</b>Criação de uma aplicação móvel com design
                minimalista e recursos interativos.
            </p>
            <p>
                <b>Site institucional:</b>Constução de um site institucional moderno e
                responsivo para melhorar a presença digital.
            </p>
        </div>
        <button class="btn btn-primary mt-3" type="button">
            Conheça o meu trabalho
        </button>
        <h3 class="mt-3">Experiência profissional</h3>
        <hr />
        <div class="row mt-3">
            <div class="col-sm-5">
                <img src="Imagens/Workday.jpg" alt="Img-estudando" class="img-estudando" />
            </div>
            <div class="col-sm-7 mt-3">
                <p>
                    <b>Web Developerr Sênior:</b>Lidero projetos complexos para clientes
                    de alto perfil.
                </p>
                <p>
                    <b>Freelancer de Desenvolvimento Web:</b>Trabalhei com diversos
                    clientes, desde pequenos empresas até startups, entregando soluções
                    personalizadas.
                </p>
                <p>
                    <b>Estágio de Desenvolvimento:</b>Iniciei a minha experiência
                    profissional numa empresa de tecnologia.
                </p>
                <button class="btn btn-primary mt-3" type="button">
                    CV Download
                </button>
            </div>
        </div>
        <h3 class="mt-3">Serviços oferecidos</h3>
        <hr />
        <div class="row mt-3">
            <div class="col-sm-6 services">
                <h3>Desenvolvimento Web</h3>
                <p>
                    Construo sites e aplicações web responsivas e de alto desempenho.
                </p>
            </div>
            <div class="col-sm-6 services">
                <h3>Design de UX/UI</h3>
                <p>
                    Construo sites e aplicações web responsivas e de alto desempenho.
                </p>
            </div>
            <div class="col-sm-6 services">
                <h3>Desenvolvimento Mobile</h3>
                <p>
                    Construo sites e aplicações web responsivas e de alto desempenho.
                </p>
            </div>
            <div class="col-sm-6 services">
                <h3>Integração back-end</h3>
                <p>
                    Construo sites e aplicações web responsivas e de alto desempenho.
                </p>
            </div>
        </div>

        <div class="clients mt-3">
            <h3>O que dizem os meus clientes</h3>
            <hr />

            <!-- Cards com Lightbox integrado -->
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <div class="card">
                        <a href="Imagens/cliente1.jpg" data-lightbox="clientes" data-title="João Silva">
                            <img src="Imagens/cliente1.jpg" class="card-img-top" alt="João Silva" />
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">João Silva</h5>
                            <p class="card-text">
                                "Excelente profissional e entrega de forma pontual."
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 mb-3">
                    <div class="card">
                        <a href="Imagens/cliente2.jpg" data-lightbox="clientes" data-title="Ana Maria">
                            <img src="Imagens/cliente2.jpg" class="card-img-top" alt="Ana Maria" />
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">Ana Maria</h5>
                            <p class="card-text">"Profissional competente e atencioso"</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 mb-3">
                    <div class="card">
                        <a href="Imagens/cliente3.jpg" data-lightbox="clientes" data-title="Juliana Fernandez">
                            <img src="Imagens/cliente3.jpg" class="card-img-top" alt="Juliana Fernandez" />
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">Juliana Fernandez</h5>
                            <p class="card-text">"Inovador e interessado."</p>
                        </div>
                    </div>
                </div>
            </div>
            <h3>Vamos conversar</h3>
            <hr />
            <div class="row">
                <div class="col-sm-4 clients mt-3">
                    <h3>Envie uma mensagem</h3>
                    <p>
                        Entre em contacto através do meu e-mail ou pelas redes sociais.
                    </p>
                </div>
                <div class="col-sm-4 clients mt-3">
                    <h3>Agende uma reunião</h3>
                    <p>Marque uma reunião para discutir seu projeto em detalhes.</p>
                </div>
                <div class="col-sm-4 clients mt-3">
                    <h3>Conecte-se comigo</h3>
                    <p>Siga-me nas redes sociais para acompanha minhas novidades.</p>
                </div>
            </div>
            <div class="contact-me text-center">
                <a href="mailto:joedoe@gmail.com">
                    <button class="btn btn-primary mt-5 mb-5" type="button">
                        Entre em contacto
                    </button>
                </a>
            </div>
        </div>
    </div>
    <footer class="footer text-black py-3">
        <div class="container text-center">
            <p>
                &copy; Rhudan Moura 2025 | Web Developer |
                <a href="politica-privacidade.html" class="textfooter">Política de Privacidade</a>
            </p>
            <div class="footer1">
                <div class="footerlinks">
                    <a href="mailto:seuemail@dominio.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                <div class="footerlinks">
                    <a href="https://facebook.com/seuperfil" target="_blank" title="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                </div>
                <div class="footerlinks">
                    <a href="https://instagram.com/seuperfil" target="_blank" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                <div class="footerlinks">
                    <a href="tel:+123456789" title="Telefone">
                        <i class="fas fa-phone" id="phone"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
</body>

</html>