<?php
// admin/edit_user.php
session_start();
require_once '../php/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = '';
$user = null;

if (isset($_GET['id'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $user = $stmt->fetch();
        if (!$user)
            die("Usuário não encontrado.");
    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = $_POST['email'];
        $userType = $_POST['user_type'];
        $isActive = $_POST['is_active'];

        // Proteção anti-bloqueio próprio
        if ($user['id'] == $_SESSION['user_id'] && $isActive == 0) {
            $message = '<div class="alert alert-warning">Você não pode desativar a si mesmo!</div>';
        } else {
            $sql = "UPDATE users SET first_name=?, last_name=?, email=?, user_type=?, is_active=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$firstName, $lastName, $email, $userType, $isActive, $_GET['id']]);
            $message = '<div class="alert alert-success">Usuário atualizado! <a href="dashboard.php">Voltar</a></div>';

            // Atualizo a tela
            $user['first_name'] = $firstName;
            $user['last_name'] = $lastName;
            $user['email'] = $email;
            $user['user_type'] = $userType;
            $user['is_active'] = $isActive;
        }
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../index.css">
</head>

<body>
    <?php $root_path = '../';
    include '../components/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0">Editar Usuário #<?= $user['id'] ?></h4>
                    </div>
                    <div class="card-body p-4">
                        <?= $message ?>
                        <form method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Nome</label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Apelido</label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label>Tipo</label>
                                    <select name="user_type" class="form-select">
                                        <option value="client" <?= $user['user_type'] == 'client' ? 'selected' : '' ?>>
                                            Cliente</option>
                                        <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>
                                            Administrador</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="1" <?= $user['is_active'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                        <option value="0" <?= $user['is_active'] == 0 ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>