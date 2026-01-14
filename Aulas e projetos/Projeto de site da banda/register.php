<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesindex.css?v=1" />
    <title>Ed Sheeran</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <header>
        <nav class="navbar bg-body-tertiary fixed-top" style="background-image: url(Imagens/albumX.jpg)">
            <div class="container-fluid">
                <a class="navbar-brand fs-2" href="indexpag.php">Ed Sheeran</a>

                <div class="d-flex ms-auto align-items-center">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="text-white me-5" title="Login">
                            <i class="fas fas fa-user-circle fa-lg"></i>
                        </a>
                    <?php else: ?>
                        <a href="logout.php" class="text-white me-5" title="Logout">
                            <i class="fas fa-right-to-bracket fa-lg"></i>
                        </a>
                    <?php endif; ?>

                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                        style="background-image: url(Imagens/albumX.jpg)">
                        <div class="offcanvas-header">
                            <a class="navbar-brand fs-2">Ed Sheeran</a>
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

    <!-- Main content -->
    <main class="flex-fill" style="padding-top: 100px;">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Registo de Utilizador</h3>

                            <!-- MENSAGEM DE RESPOSTA -->
                            <?php if (isset($_SESSION['register_response'])): ?>
                                <div
                                    class="alert alert-<?= $_SESSION['register_response']['success'] ? 'success' : 'danger' ?> text-center">
                                    <?= htmlspecialchars($_SESSION['register_response']['message']) ?>
                                </div>
                                <?php unset($_SESSION['register_response']); ?>
                            <?php endif; ?>

                            <form action="process_register.php" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <label for="username" class="form-label">Nome de Utilizador</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                    <div class="invalid-feedback">O nome de utilizador deve ter pelo menos 3 caracteres.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Digite um email válido.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback">A senha deve ter pelo menos 6 caracteres.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Senha</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required>
                                    <div class="invalid-feedback">As senhas não coincidem.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="user_type" class="form-label">Tipo de Utilizador</label>
                                    <select class="form-select" id="user_type" name="user_type" required>
                                        <option value="">-- Selecione --</option>
                                        <option value="user">Utilizador</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                    <div class="invalid-feedback">Selecione o tipo de utilizador.</div>
                                </div>

                                <!-- Campo do código de admin (inicialmente escondido) -->
                                <div class="mb-3" id="admin-code-field" style="display: none;">
                                    <label for="admin_code" class="form-label">Código de Administrador</label>
                                    <input type="password" class="form-control" id="admin_code" name="admin_code">
                                    <div class="form-text">Digite o código fornecido para administradores.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="profile_pic" class="form-label">Foto de Perfil</label>
                                    <input class="form-control" type="file" id="profile_pic" name="profile_pic"
                                        accept="image/*" required>
                                    <div class="invalid-feedback">Selecione uma foto de perfil.</div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Registar</button>
                                </div>

                            </form>

                            <script>
                                document.getElementById("user_type").addEventListener("change", function () {
                                    const adminField = document.getElementById("admin-code-field");
                                    if (this.value === "admin") {
                                        adminField.style.display = "block";
                                        document.getElementById("admin_code").setAttribute("required", "required");
                                    } else {
                                        adminField.style.display = "none";
                                        document.getElementById("admin_code").removeAttribute("required");
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-4 text-white"
        style="background-image: url(Imagens/albumX.jpg); background-size: cover; background-position: center;">
        <div class="container text-center">
            &copy; Rhudan Moura 2025 | Web Developer |
            <a href="politica-privacidade.html" class="text-white text-decoration-none">Política de Privacidade</a>
            <div class="d-flex justify-content-center gap-3 mt-2">
                <a href="https://www.facebook.com/EdSheeranMusic/" target="_blank"><i
                        class="fab fa-facebook fa-lg"></i></a>
                <a href="https://www.instagram.com/teddysphotos/" target="_blank"><i
                        class="fab fa-instagram fa-lg"></i></a>
                <a href="https://www.youtube.com/user/EdSheeran" target="_blank"><i
                        class="fab fa-youtube fa-lg"></i></a>
                <a href="https://open.spotify.com/intl-pt/artist/6eUKZXaKkcviH0Ku9w2n3V" target="_blank"><i
                        class="fab fa-spotify fa-lg"></i></a>
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
</body>

</html>