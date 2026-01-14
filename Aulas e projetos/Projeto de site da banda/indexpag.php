<?php
session_start();

include 'conexao.php';
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';
$sql = $cat
    ? $conexao->prepare("SELECT * FROM products WHERE category = ?")
    : $conexao->prepare("SELECT * FROM products");
if ($cat)
    $sql->bind_param("s", $cat);
$sql->execute();
$produtos = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ed Sheeran</title>
    <meta name="description"
        content="Site profissional do Ed Sheeran. Ed Sheeran é um cantor, compositor e produtor britânico. Ao longo de sua carreira, ele alcançou enorme sucesso global com álbuns como X (multiply), ÷ (divide), e seus singles, especialmente Shape of You. Ed Sheeran também fez parte de turnês de artistas como Taylor Swift e One Direction. Em 2024, ele foi reconhecido como uma das maiores estrelas pop da Billboard." />
    <meta name="keywords" content="Ed Sheeran, Artista, Musica, Tour, Billboard, Prêmiações, Cantor." />
    <meta name="author" content="Rhudan Moura" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="stylesindex.css?v=1" />
</head>

<body class="bg-cover" style="background-image: url('Imagens/ed-taylor.jpg');">

    <header>
        <nav class="navbar bg-body-tertiary fixed-top" style="background-image: url(Imagens/albumX.jpg)">
            <div class="container-fluid">
                <a class="navbar-brand fs-2" href="indexpag.php">Ed Sheeran</a>

                <div class="d-flex ms-auto align-items-center">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Se não está logado → mostra ícone de login -->
                    <a href="login.php" class="text-white me-5" title="Login">
                        <i class="fas fas fa-user-circle fa-lg"></i>
                    </a>
                    <?php else: ?>
                    <!-- Se está logado → mostra ícone de logout -->
                    <a href="logout.php" class="text-white me-5" title="Logout"
                        onclick="return confirm('Tem certeza que deseja sair?')">
                        <i class="fas fa-right-to-bracket fa-lg"></i>
                    </a>

                    <!-- Mostra link de admin APENAS se o user_type for admin -->
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                    <div class="adminLink">
                        <a href="admin.php" class="nav-link ms-3">Admin</a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>


                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                        style="background-image: url(Imagens/albumX.jpg)">
                        <div class="offcanvas-header">
                            <a class="navbar-brand fs-2" s>Ed Sheeran</a>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                                <li class="nav-item fs-2">
                                    <a class="nav-link active" aria-current="page" href="#about-ed">Sobre</a>
                                </li>
                                <li class="nav-item fs-2">
                                    <a class="nav-link" href="#music-ed">Musicas</a>
                                </li>
                                <li class="nav-item fs-2">
                                    <a class="nav-link" href="#tour-ed">Tour</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
        </nav>
    </header>

    <main>
        <div class="container min-vh-100 mt-5 pt-5">
            <!-- Carousel -->
            <div id="carouselExampleAutoplay" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="Imagens/Ed-Tour.png" class="d-block w-100" alt="Imagem 1" />
                    </div>
                    <div class="carousel-item">
                        <img src="Imagens/Ed-image2.jpg" class="d-block w-100" alt="Imagem 2" />
                    </div>
                    <div class="carousel-item">
                        <img src="Imagens/Ed-image3.jpg" class="d-block w-100" alt="Imagem 3" />
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplay"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplay"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            <!-- Sobre Ed Sheeran -->
            <section id="about-ed" class="mt-5 p-3 bg-white rounded shadow">
                <h2>Ed. Sheeran</h2>
                <p>
                    Ed Sheeran é um cantor, compositor e produtor britânico, nascido
                    em 17 de fevereiro de 1991, em Halifax. Ele ganhou destaque após o
                    lançamento de seu EP independente em 2012, que chamou a atenção de
                    grandes nomes como Elton John e Jamie Foxx. Seu álbum de estreia,
                    + (plus), foi um enorme sucesso, com singles como "The A Team" e
                    "Lego House", e fez de Sheeran um dos artistas mais premiados da
                    sua geração, incluindo dois Brit Awards em 2012. Ao longo de sua
                    carreira, ele alcançou enorme sucesso global com álbuns como X
                    (multiply), ÷ (divide), e seus singles, especialmente "Shape of
                    You", que lideraram as paradas internacionais. Ed Sheeran também
                    fez parte de turnês de artistas como Taylor Swift e One Direction,
                    além de realizar shows icônicos, como três apresentações no
                    Estádio de Wembley. Em 2024, ele foi reconhecido como uma das
                    maiores estrelas pop da Billboard.
                </p>
            </section>
            <hr />

            <!-- Loja Virtual -->
            <section id="storeVirtual" class="mt-5">
                <!-- CSS de reforço para centralização -->
                <style>
                /* garante que qualquer regra externa não quebre a centralização */
                #storeVirtual .wrap {
                    max-width: 1200px;
                    margin: 0 auto;
                    /* centraliza horizontalmente */
                    padding-left: 12px;
                    padding-right: 12px;
                }

                /* se algum CSS externo mexeu no .row/.container, isso ajuda */
                #storeVirtual .row {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                }
                </style>

                <div class="wrap">
                    <!-- Título -->
                    <div class="text-center mb-4 p-2 bg-white bg-opacity-75 rounded">
                        <h1 class="text-dark m-0">Loja Online</h1>
                    </div>

                    <!-- Filtro de categorias -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block mb-2">Filtrar por Categorias</label>
                        <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                            <a class="btn btn-outline-dark btn-sm" href="indexpag.php">Todas</a>
                            <a class="btn btn-outline-dark btn-sm" href="indexpag.php?cat=cds">CDs</a>
                            <a class="btn btn-outline-dark btn-sm" href="indexpag.php?cat=merch">Merch</a>
                        </div>
                    </div>

                    <!-- Grid de produtos centralizado -->
                    <div id="product-list"
                        class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
                        <?php while ($p = $produtos->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($p['name']) ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center"><?= htmlspecialchars($p['name']) ?></h5>
                                    <p class="card-text small mb-2 text-muted text-center">
                                        <?= htmlspecialchars($p['description']) ?>
                                    </p>
                                    <!-- Preço e input de quantidade -->
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                                        <p class="fw-bold mb-0 text-dark">
                                            €<?= number_format($p['price'], 2, ',', '.') ?></p>
                                        <input type="number" id="qty-<?= (int) $p['id'] ?>" value="1" min="1"
                                            class="form-control form-control-sm text-center" style="width:70px;">
                                    </div>

                                    <div class="mt-auto d-flex gap-2 justify-content-center">
                                        <!-- Ver Detalhes (usa a função de lightbox que já tens) -->
                                        <button class="btn btn-outline-secondary btn-sm" onclick="abrirLightbox(
          '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>',
          '<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>',
          '<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>',
          '<?= number_format($p['price'], 2, ',', '.') ?>'
        )">
                                            Ver Detalhes
                                        </button>



                                        <?php if (isset($_SESSION['user_id'])): ?>
                                        <button type="button" class="btn btn-dark btn-sm"
                                            onclick="adicionarCarrinho(event, <?= (int) $p['id'] ?>)">
                                            🛒 Adicionar
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="verificarLogin()">
                                            Faça o Login para comprar
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>



            <!-- Lightbox mínimo (reutiliza teu JS atual) -->
            <div id="lightbox"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); align-items:center; justify-content:center; z-index:2000;">
                <div class="bg-white p-3 rounded" style="max-width:600px; width:90%; position:relative;">
                    <!-- adicionar classe close-btn para evitar erro se lojaonline.js procura por .close-btn -->
                    <button class="btn-close close-btn" style="position:absolute; top:10px; right:10px"
                        onclick="fecharLightbox()"></button>
                    <h4 id="lightbox-title" class="mb-2"></h4>
                    <img id="lightbox-image" src="" alt="" class="img-fluid mb-2">
                    <p id="lightbox-description" class="mb-1"></p>
                    <p id="lightbox-price" class="fw-bold"></p>
                </div>
            </div>

            <script>
            function abrirLightbox(titulo, imagem, descricao, preco) {
                document.getElementById("lightbox-title").textContent = titulo;
                document.getElementById("lightbox-image").src = imagem;
                document.getElementById("lightbox-description").textContent = descricao;
                document.getElementById("lightbox-price").textContent = "Preço: €" + preco;
                document.getElementById("lightbox").style.display = "flex";
            }

            function fecharLightbox() {
                document.getElementById("lightbox").style.display = "none";
            }
            </script>
    </main>


    <section id="tour-ed" class="min-vh-100 mt-5">
        <div class="container">
            <h2 class="text-center mb-4 text-white">Tour</h2>

            <div class="list-group">
                <!-- Ingresso 1 -->
                <div
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between mb-2 p-3 border rounded shadow-sm">
                    <div class="col-12 col-sm-2 text-center mb-2 mb-sm-0">
                        <h5>12 FEB<br>2025</h5>
                    </div>
                    <div class="col-12 col-sm-8 text-center text-sm-start mb-2 mb-sm-0">
                        <h5><strong>JN Stadium</strong><br><i>Shillong, India</i></h5>
                    </div>
                    <div class="col-12 col-sm-2 text-center">
                        <a href="https://in.bookmyshow.com/events/ed-sheeran-india-tour-2025/ET00421192?webview=true"
                            target="_blank" class="btn btn-dark w-100">Ingresso</a>
                    </div>
                </div>

                <!-- Ingresso 2 -->
                <div
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between mb-2 p-3 border rounded shadow-sm">
                    <div class="col-12 col-sm-2 text-center mb-2 mb-sm-0">
                        <h5>15 FEB<br>2025</h5>
                    </div>
                    <div class="col-12 col-sm-8 text-center text-sm-start mb-2 mb-sm-0">
                        <h5><strong>Leisure Valley Ground</strong><br><i>Delhi NCR, India</i></h5>
                    </div>
                    <div class="col-12 col-sm-2 text-center">
                        <a href="https://in.bookmyshow.com/events/ed-sheeran-india-tour-2025/ET00421192?webview=true"
                            target="_blank" class="btn btn-dark w-100">Ingresso</a>
                    </div>
                </div>

                <!-- Ingresso 3 -->
                <div
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between mb-2 p-3 border rounded shadow-sm">
                    <div class="col-12 col-sm-2 text-center mb-2 mb-sm-0">
                        <h5>24 FEB<br>2025</h5>
                    </div>
                    <div class="col-12 col-sm-8 text-center text-sm-start mb-2 mb-sm-0">
                        <h5><strong>Hangzou Olympic Sports Centre Gymnasium</strong><br><i>Hangzhou, China</i></h5>
                    </div>
                    <div class="col-12 col-sm-2 text-center">
                        <button class="btn btn-secondary w-100" disabled>Esgotado</button>
                    </div>
                </div>

                <!-- Ingresso 4 -->
                <div
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between mb-2 p-3 border rounded shadow-sm">
                    <div class="col-12 col-sm-2 text-center mb-2 mb-sm-0">
                        <h5>25 FEB<br>2025</h5>
                    </div>
                    <div class="col-12 col-sm-8 text-center text-sm-start mb-2 mb-sm-0">
                        <h5><strong>Hangzou Olympic Sports Centre Gymnasium</strong><br><i>Hangzhou, China</i></h5>
                    </div>
                    <div class="col-12 col-sm-2 text-center">
                        <button class="btn btn-secondary w-100" disabled>Esgotado</button>
                    </div>
                </div>

                <!-- Ingresso 5 -->
                <div
                    class="list-group-item d-flex flex-wrap align-items-center justify-content-between mb-2 p-3 border rounded shadow-sm">
                    <div class="col-12 col-sm-2 text-center mb-2 mb-sm-0">
                        <h5>26 FEB<br>2025</h5>
                    </div>
                    <div class="col-12 col-sm-8 text-center text-sm-start mb-2 mb-sm-0">
                        <h5><strong>Hangzou Olympic Sports Centre Gymnasium</strong><br><i>Hangzhou, China</i></h5>
                    </div>
                    <div class="col-12 col-sm-2 text-center">
                        <button class="btn btn-secondary w-100" disabled>Esgotado</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background-image: url(Imagens/albunsimage.jpg); background-size: cover;">
        <div class="container" id="music-ed">
            <h3 class="text-album">Albúm</h3>

            <div class="row">
                <div class="col-sm-4 mb-3">
                    <div class="image-container">
                        <a href="https://www.youtube.com/watch?v=pbtpQHcUSzU&list=OLAK5uy_kBG_zeoDnFxMeNTRplq1rNJQo3_mUmdic"
                            target="_blank">
                            <img src="Imagens/+EdSheeran.jpg" class="img-fluid" alt="Imagem com Link" />
                        </a>
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="image-container">
                        <a href="https://www.youtube.com/watch?v=ZpbyMClPnic&list=PLjp0AEEJ0-fEpN_3LiEEcNqua_SDx4PMi"
                            target="_blank">
                            <img src="Imagens/albumX.jpg" class="img-fluid" alt="Imagem com Link" />
                        </a>
                    </div>
                </div>
                <div class="col-sm-4 mb-3">
                    <div class="image-container">
                        <a href="https://www.youtube.com/watch?v=OjGrcJ4lZCc&list=PLZHFAlj_F3Sbpfn4mWGIK5_qo9jTKfBWM"
                            target="_blank">
                            <img src="Imagens/DivideAlbum.jpg" class="img-fluid" alt="Imagem com Link" />
                        </a>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 col-6 mx-auto mt-3">
                <a class="btn btn-dark btn-contact d-block" href="contactme.html">Entre em contacto</a>
            </div>
        </div>
    </section>

    <footer class="footer text-black py-4" style="background-image: url(Imagens/albumX.jpg); background-size: cover;">
        <div class="container text-center">
            <p class="mb-3">
                &copy; Rhudan Moura 2025 | Web Developer |
                <a href="politica-privacidade.html" class="textfooter">Política de Privacidade</a>
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="https://www.facebook.com/EdSheeranMusic/" target="_blank" title="Facebook" class="footerlinks">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="https://www.instagram.com/teddysphotos/" target="_blank" title="Instagram" class="footerlinks">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="https://www.youtube.com/user/EdSheeran" target="_blank" title="YouTube" class="footerlinks">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>
                <a href="https://open.spotify.com/intl-pt/artist/6eUKZXaKkcviH0Ku9w2n3V" target="_blank" title="Spotify"
                    class="footerlinks">
                    <i class="fab fa-spotify fa-lg"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="lojaonline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script>
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    async function adicionarCarrinho(event, productId) {
        event.preventDefault();

        try {
            const qtyInput = document.getElementById("qty-" + productId);
            const qty = Math.max(1, parseInt(qtyInput ? qtyInput.value : 1, 10));

            const formData = new FormData();
            formData.append("product_id", productId);
            formData.append("qty", qty);

            const res = await fetch("add_to_cart.php", {
                method: "POST",
                body: formData,
                credentials: "same-origin"
            });

            let data;
            try {
                data = await res.json();
            } catch (err) {
                throw new Error("Resposta inválida do servidor.");
            }

            if (!res.ok || !data.success) {
                const message = data && data.message ? data.message : "Erro ao adicionar produto.";
                showMessageModal("Erro", message);
                return false;
            }

            // Atualiza modal com nome, imagem e quantidade
            document.getElementById("confirm-message").innerHTML = `
      <div class="d-flex align-items-center">
        <img src="${escapeHtml(data.item.image)}" alt="${escapeHtml(data.item.name)}"
             style="width:64px;height:64px;object-fit:cover;border-radius:6px;margin-right:12px;">
        <div>
          <strong>${escapeHtml(data.item.name)}</strong><br>
          ${qty} unidade(s) adicionada(s)
        </div>
      </div>
    `;

            // Atualiza badge de carrinho se existir
            const badge = document.getElementById("cart-count");
            if (badge && typeof data.cartCount !== "undefined") {
                badge.textContent = data.cartCount;
            }

            const modalEl = document.getElementById("confirmModal");
            bootstrap.Modal.getOrCreateInstance(modalEl).show();

        } catch (err) {
            console.error(err);
            showMessageModal("Erro", "Não foi possível adicionar o produto. Tente novamente.");
        }
    }

    function showMessageModal(title, message) {
        let el = document.getElementById("messageModal");
        if (!el) {
            el = document.createElement("div");
            el.innerHTML = `
    <div class="modal fade" id="messageModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${escapeHtml(title)}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>${escapeHtml(message)}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>`;
            document.body.appendChild(el);
        } else {
            el.querySelector(".modal-title").textContent = title;
            el.querySelector(".modal-body p").textContent = message;
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById("messageModal")).show();
    }
    </script>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Cabeçalho do modal -->
                <div class="modal-header">
                    <h5 class="modal-title">✅ Produto adicionado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Corpo do modal (texto dinâmico) -->
                <div class="modal-body">
                    <p id="confirm-message" class="mb-0"></p>
                </div>

                <!-- Rodapé com botões -->
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Continuar comprando
                    </button>
                    <a href="cart.php" class="btn btn-dark">Ir para o carrinho</a>
                </div>
            </div>
        </div>
    </div>



</body>

</html>