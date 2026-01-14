<?php
session_start();
session_unset(); // limpa variáveis de sessão
session_destroy(); // destrói a sessão

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}


header("Location: indexpag.php");
exit;
?>