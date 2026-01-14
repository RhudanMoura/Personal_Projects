<?php
// usuarios.php

// 1) Inclui a conexão (faça esse arquivo normalmente)
include 'conexao.php';

// 2) Processamento do POST - deve ficar antes de qualquer saída HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validações simples
    if (empty($_POST['Login'])) {
        header("Location: usuarios.php?mensagem=" . urlencode("O campo Login não pode estar vazio."));
        exit;
    }
    if (empty($_POST['Senha'])) {
        header("Location: usuarios.php?mensagem=" . urlencode("O campo Senha não pode estar vazio."));
        exit;
    }

    // Coleta e prepara dados
    $nome = trim($_POST['Nome'] ?? '');
    $login = trim($_POST['Login'] ?? '');
    // Use password_hash (mais seguro que hash('sha512', ...))
    $senhaHash = password_hash($_POST['Senha'], PASSWORD_DEFAULT);
    $ativo = isset($_POST['Ativo']) ? 1 : 0;

    // --- Upload seguro de imagem ---
    $imagemPath = ''; // string que será salva no DB (ex.: "img/abc.jpg")
    if (isset($_FILES['Imagem']) && $_FILES['Imagem']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['Imagem']['tmp_name'];
        $origName = basename($_FILES['Imagem']['name']);
        // Verifica se é imagem
        if (false === getimagesize($tmpName)) {
            header("Location: usuarios.php?mensagem=" . urlencode("O arquivo enviado não é uma imagem válida."));
            exit;
        }
        // Checa extensão permitida
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            header("Location: usuarios.php?mensagem=" . urlencode("Formato de imagem não permitido."));
            exit;
        }
        // Gera nome único e move o arquivo
        $targetDir = __DIR__ . '/img/';
        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);
        $newName = uniqid('img_') . '.' . $ext;
        $destFull = $targetDir . $newName;
        if (move_uploaded_file($tmpName, $destFull)) {
            // caminho relativo salvo no banco
            $imagemPath = 'img/' . $newName;
        } else {
            header("Location: usuarios.php?mensagem=" . urlencode("Falha ao salvar a imagem."));
            exit;
        }
    }

    // --- Inserção usando prepared statement para evitar SQL Injection ---
    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, login, senha, ativo, imagem) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        header("Location: usuarios.php?mensagem=" . urlencode("Erro no banco: " . $conexao->error));
        exit;
    }
    // tipos: s = string, i = integer. ordem: nome(s), login(s), senha(s), ativo(i), imagem(s)
    $stmt->bind_param('sssis', $nome, $login, $senhaHash, $ativo, $imagemPath);

    if ($stmt->execute()) {
        // Sucesso -> PRG: redireciona para GET (evita reenvio com F5)
        header("Location: usuarios.php?mensagem=" . urlencode("Usuário inserido com sucesso."));
        exit;
    } else {
        header("Location: usuarios.php?mensagem=" . urlencode("Erro ao inserir usuário: " . $stmt->error));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <title>Portfólio - Usuários</title>
</head>

<body>
    <div class="container mt-4">
        <?php
        // Exibe mensagem segura (escaped)
        if (isset($_GET['mensagem']) && $_GET['mensagem'] !== '') {
            echo '<div class="alert alert-warning">' . htmlspecialchars($_GET['mensagem'], ENT_QUOTES, 'UTF-8') . '</div>';
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Cadastro do usuário</h3>
            </div>
            <div class="card-body">
                <form action="usuarios.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="Nome" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Login</label>
                        <input type="text" name="Login" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="Senha" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <input type="file" name="Imagem" class="form-control" accept="image/*">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="Ativo" class="form-check-input" id="ativoCheck">
                        <label class="form-check-label" for="ativoCheck">Ativo</label>
                    </div>
                    <button class="btn btn-success" type="submit">Inserir usuário</button>
                </form>
            </div>
        </div>

        <!-- Tabela de usuários -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>Login</th>
                    <th>Ativo</th>
                    <th>Imagem de perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Busca e exibe usuários (escape ao mostrar)
                $query = "SELECT id, nome, login, ativo, imagem FROM usuarios";
                $dados = mysqli_query($conexao, $query);
                if ($dados) {
                    while ($linha = mysqli_fetch_assoc($dados)) {
                        echo '<tr>';
                        echo '<td>' . (int) $linha['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($linha['nome'], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($linha['login'], ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . ($linha['ativo'] ? '<input type="checkbox" disabled checked>' : '<input type="checkbox" disabled>') . '</td>';
                        $img = $linha['imagem'] ? htmlspecialchars($linha['imagem'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/100x70?text=Sem+foto';
                        echo '<td><img src="' . $img . '" width="100" height="70" alt="Foto"></td>';
                        echo '<td>
                            <a class="btn btn-warning btn-sm" href="./usuarioEditar.php?id=' . (int) $linha['id'] . '">Editar</a>
                            <a class="btn btn-danger btn-sm" href="./usuarioExcluir.php?id=' . (int) $linha['id'] . '">Excluir</a>
                          </td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
</body>

</html>