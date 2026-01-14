<?php
session_start();
include("conexao.php");

// 1️⃣ Verificação de acesso → só admin pode entrar
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: indexpag.php");
    exit;
}

// ---- UTILIZADORES ----
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['delete_user_id']);
    $conexao->query("DELETE FROM users WHERE id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['promote_user'])) {
    $id = intval($_POST['promote_user_id']);
    $conexao->query("UPDATE users SET user_type = 'admin' WHERE id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ---- PRODUTOS ----
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $uploadDir = "img_adm/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imgPath = $uploadDir . basename($_FILES['image']['name']);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $imgPath)) {
        $conexao->query("INSERT INTO products (name, description, price, image) 
                         VALUES ('$name', '$desc', '$price', '$imgPath')");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['delete_product'])) {
    $id = intval($_POST['delete_product_id']);
    $conexao->query("DELETE FROM products WHERE id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ---- ENCOMENDAS ----
if (isset($_POST['process_order'])) {
    $id = intval($_POST['order_id']);
    $conexao->query("UPDATE orders SET status = 'Processado' WHERE id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    @font-face {
        font-family: 'JMHTypewriter';
        src: url('Fontes/JMH Typewriter-Black.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
    }

    .navbar-ed {
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .navbar-ed .container {
        display: flex;
        justify-content: center;
        /* centraliza horizontal */
        align-items: center;
        /* centraliza vertical */
    }

    .navbar-ed .brand a {
        font-family: 'JMHTypewriter', monospace;
        font-size: 2.5rem;
        color: #1DB954;
        letter-spacing: 2px;
        text-decoration: none;
    }

    .navbar-ed .brand a:hover {
        color: #1ed760;
    }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar-ed">
        <div class="container">
            <span class="brand"><a href="indexpag.php">Ed. Sheeran</a></span>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Painel de Administração</h1>

        <!-- UTILIZADORES -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Gestão de Utilizadores</div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                    <?php
                    $users = $conexao->query("SELECT * FROM users");
                    while ($u = $users->fetch_assoc()) {
                        echo "<tr>
                            <td>{$u['id']}</td>
                            <td>{$u['username']}</td>
                            <td>{$u['email']}</td>
                            <td>{$u['user_type']}</td>
                            <td>
                                <form method='POST' style='display:inline'>
                                    <input type='hidden' name='delete_user_id' value='{$u['id']}'>
                                    <button type='submit' name='delete_user' class='btn btn-danger btn-sm'>Remover</button>
                                </form>
                                <form method='POST' style='display:inline'>
                                    <input type='hidden' name='promote_user_id' value='{$u['id']}'>
                                    <button type='submit' name='promote_user' class='btn btn-warning btn-sm'>Tornar Admin</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <!-- PRODUTOS -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Gestão de Produtos</div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" class="mb-3">
                    <div class="row g-2">
                        <div class="col"><input type="text" name="name" placeholder="Nome" class="form-control"
                                required></div>
                        <div class="col"><input type="text" name="description" placeholder="Descrição"
                                class="form-control" required></div>
                        <div class="col"><input type="number" step="0.01" name="price" placeholder="Preço"
                                class="form-control" required></div>
                        <div class="col"><input type="file" name="image" class="form-control" required></div>
                        <div class="col"><button type="submit" name="add_product"
                                class="btn btn-success">Adicionar</button></div>
                    </div>
                </form>

                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                    <?php
                    $products = $conexao->query("SELECT * FROM products");
                    while ($p = $products->fetch_assoc()) {
                        echo "<tr>
                            <td>{$p['id']}</td>
                            <td>{$p['name']}</td>
                            <td>€{$p['price']}</td>
                            <td><img src='{$p['image']}' width='50'></td>
                            <td>
                                <form method='POST' style='display:inline'>
                                    <input type='hidden' name='delete_product_id' value='{$p['id']}'>
                                    <button type='submit' name='delete_product' class='btn btn-danger btn-sm'>Remover</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <!-- ENCOMENDAS -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">Gestão de Encomendas</div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Utilizador</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    <?php
                    $orders = $conexao->query("SELECT orders.*, users.username 
                           FROM orders 
                           LEFT JOIN users ON orders.user_id = users.id");
                    while ($o = $orders->fetch_assoc()) {
                        $isProcessed = strtolower($o['status']) === 'processado';
                        $userDisplay = $o['username'] ?? "Visitante";

                        echo "<tr>
        <td>{$o['id']}</td>
        <td>{$userDisplay}</td>
        <td>€{$o['total']}</td>
        <td>{$o['status']}</td>
        <td>
            <form method='POST'>
                <input type='hidden' name='order_id' value='{$o['id']}'>
                <button type='submit' name='process_order' class='btn btn-primary btn-sm' " . ($isProcessed ? "disabled title='Já processado'" : "") . ">
                    Processar
                </button>
            </form>
        </td>
    </tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

    </div>
</body>

</html>