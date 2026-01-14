<?php

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    include "./conexao.php";

    $query = "Delete from usuarios where id = " . $_GET["id"];
    $resultado = mysqli_query($conexao, $query);

    if ($resultado) {
        header("Location: ./usuarios.php?mensagem=Excluido com sucesso");
        exit();
    } else {
        header("Location: ./usuarios.php?mensagem=Ocorreu erro ao excluir");
        exit();
    }
} else {
    header("Location: ./usuarios.php?mensagem=Selecione um usuario para apagar");
    exit();
}

?>