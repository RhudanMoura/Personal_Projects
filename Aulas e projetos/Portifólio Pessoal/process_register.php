<?php
session_start();
include("conexao.php");

// Carrega mensagens do arquivo JSON
$responses = json_decode(file_get_contents("responses.json"), true);

// Resposta padrão
$response = ["success" => false, "message" => $responses["errors"]["insert_error"]];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type'];
    $profile_pic = $_FILES['profile_pic'];

    // Validações
    if (strlen($username) < 3) {
        $response["message"] = $responses["errors"]["username_short"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = $responses["errors"]["email_invalid"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 6) {
        $response["message"] = $responses["errors"]["password_short"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $response["message"] = $responses["errors"]["password_mismatch"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    if (empty($user_type)) {
        $response["message"] = $responses["errors"]["user_type_required"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    if ($profile_pic['error'] !== UPLOAD_ERR_OK) {
        $response["message"] = $responses["errors"]["no_profile_pic"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    // Verificar se username ou email já existem
    $check = $conexao->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $response["message"] = $responses["errors"]["user_exists"];
        $_SESSION['register_response'] = $response;
        header("Location: register.php");
        exit;
    }

    // Upload da imagem
    $target_dir = "img_register/";
    $target_file = $target_dir . basename($profile_pic["name"]);
    move_uploaded_file($profile_pic["tmp_name"], $target_file);

    // Inserir usuário
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = $conexao->prepare("INSERT INTO users (username, email, password_hash, user_type, profile_pic) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("sssss", $username, $email, $password_hash, $user_type, $target_file);

    if ($sql->execute()) {
        $response = ["success" => true, "message" => $responses["success"]["register_success"]];
        $_SESSION['register_response'] = $response;

        // Redireciona direto para o login
        header("Location: login.php");
        exit;
    }
}

// Se algo deu errado fora do fluxo
$_SESSION['register_response'] = $response;
header("Location: register.php");
exit;
?>