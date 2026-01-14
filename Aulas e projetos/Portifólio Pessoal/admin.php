<!DOCTYPE html>
<html lang="pt">

<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validações simples
    if (empty($_POST['nome'])) {
        header("Location: admin.php?mensagem=" . urlencode("O campo nome não pode estar vazio."));
        exit;
    }
    if (empty($_POST['descricao'])) {
        header("Location: admin.php?mensagem=" . urlencode("O campo descricao não pode estar vazio."));
        exit;
    }
    // Coleta e prepara dados
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    $imagemPath = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['imagem']['tmp_name'];
        $origName = basename($_FILES['imagem']['name']);


        if (false === getimagesize($tmpName)) {
            header("Location: admin.php?mensagem=" . urlencode("O arquivo enviado não é uma imagem válida."));
            exit;
        }

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            header("Location: admin.php?mensagem=" . urlencode("Formato de imagem não permitido."));
            exit;
        }
        // Gera nome único e move o arquivo
        $targetDir = __DIR__ . '/img_projetos/';
        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);
        $newName = uniqid('img_') . '.' . $ext;
        $destFull = $targetDir . $newName;
        if (move_uploaded_file($tmpName, $destFull)) {
            // caminho relativo salvo no banco
            $imagemPath = 'img_projetos/' . $newName;
        } else {
            header("Location: admin.php?mensagem=" . urlencode("Falha ao salvar a imagem."));
            exit;
        }
    }



    // --- Inserção usando prepared statement para evitar SQL Injection ---
    $stmt = $conexao->prepare("INSERT INTO projetos (Imagem, Nome, Descricao) VALUES (?, ?, ?)");
    if (!$stmt) {
        header("Location: admin.php?mensagem=" . urlencode("Erro no banco: " . $conexao->error));
        exit;
    }
    // tipos: s = string, i = integer. ordem: nome(s), login(s), senha(s), ativo(i), imagem(s)
    $stmt->bind_param('sss', $imagemPath, $nome, $descricao);

    if ($stmt->execute()) {
        // Sucesso -> PRG: redireciona para GET (evita reenvio com F5)
        header("Location: admin.php?mensagem=" . urlencode("Projeto inserido com sucesso."));
        exit;
    } else {
        header("Location: admin.php?mensagem=" . urlencode("Erro ao inserir projeto: " . $stmt->error));
        exit;
    }
}
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesindex.css?v=1" />
    <title>Portifólio</title>
    <meta name="description"
        content="Portifólio do Rhudan Moura. Desenvolvedor Front-end, back-end e UI-UX-Design. Crio sites responsivos e atrativos para pequenas e grandes empresas." />
    <meta name="keywords"
        content="Rhudan Moura, Desenvolvedor, Front-end, Back-end, UI-UX-Design, Portifólio, Responsivo, Pequenas empresas, Grandes empresas." />
    <meta name="Author" content="Rhudan Moura">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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
                <a href="login.php" class="login btn btn-primary text-white me-2">Login</a>
                <a href="register.php" class="register btn btn-primary text-white me-2">Register</a>
                <a href="admin.php" class="adminBtn btn btn-primary text-white">Admin</a>
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

    <div>
        <div class="admContainerTop">
            <h2>Bem-vindo à Página de Administração!</h2>
            <br>
            <h3>Adicionar Novo Projeto</h3>
            <hr>
        </div>
        <div class="admContainerMid">
            <form action="./admin.php" method="post" id="formColumn" enctype="multipart/form-data">
                <label>Nome do projeto:</label>
                <input type="text" name="nome">
                <label>Descrição do projeto:</label>
                <input type="text" name="descricao">
                <label>Imagem do projeto:</label>
                <input type="file" name="imagem" accept="image/*">
                <br>
                <button type="submit" class="btn btn-primary">Adicionar Projeto</button>
            </form>
        </div>
        <hr>
        <div class="container text-center">
            <div class="row align-items-start ">
                <div class="col-2" style="font-family: 'Times New Roman', Times, serif;">
                    <h4>Buscar projeto:</h4>
                </div>
                <div class="col-8">
                    <form action="" method="get">
                        <input name="busca"
                            value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>"
                            type="text" class="w-100">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                </div>
                </form>
            </div>
        </div>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 20%;">Nome</th>
                    <th style="width: 35%;">Descrição</th>
                    <th style="width: 35%;">Imagem</th>
                    <th style="width: 10%;"></th>
                </tr>
            </thead>
            <?php
            if (empty($_GET['busca'])) {
                $query = "SELECT id, nome, descricao, imagem FROM projetos";
                $dados = mysqli_query($conexao, $query);
                if ($dados) {
                    while ($linha = mysqli_fetch_assoc($dados)) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($linha['descricao'], ENT_QUOTES, 'UTF-8') . '</td>';
                        $img = $linha['imagem'] ? htmlspecialchars($linha['imagem'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/100x70?text=Sem+foto';
                        echo '<td><img src="' . $img . '" width="100" height="70" alt="Foto"></td>';
                        echo '<td><a class="btn btn-danger" href="./excluir.php?id=' . (int) $linha['id'] . '">Excluir</a></td>';
                        echo '</tr>';
                    }
                }
            }
            ?>

            <?php

            if (!empty($_GET['busca'])) {
                $pesquisa = $conexao->real_escape_string($_GET['busca']);
                $sql_code = "SELECT *
                                    FROM projetos
                                    WHERE Nome LIKE '%$pesquisa%'
                                    OR Descricao LIKE '%$pesquisa%'";
                $sql_query = $conexao->query($sql_code) or die("ERROR ao consultar!" . $conexao->error);

                if ($sql_query->num_rows == 0) {
                    ?>
            <tr>
                <td colspan="4">Nenhum resultado encontrado</td>
            </tr>
            <?php
                } else {
                    while ($bancodedados = $sql_query->fetch_assoc()) {
                        ?>
            <tr>
                <td><?php echo $bancodedados['Nome'] ?></td>
                <td><?php echo $bancodedados['Descricao'] ?></td>
                <td>
                    <img src="<?php echo $bancodedados['Imagem'] ? htmlspecialchars($bancodedados['Imagem'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/100x70?text=Sem+foto'; ?>"
                        width="100" height="70" alt="Foto">
                </td>
                <td>
                    <a class="btn btn-danger"
                        href="./excluir.php?id=<?php echo (int) $bancodedados['Id']; ?>">Excluir</a>
                </td>
            </tr>
            <?php
                    }
                }
                ?>

            <?php

            }
            ?>
        </table>
    </div>


</body>

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

</html>