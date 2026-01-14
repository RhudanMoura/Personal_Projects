<?php

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    include "./conexao.php";

    $query = "Delete from projetos where id = '" . $_GET["id"] . "'";
    $resultado = mysqli_query($conexao, $query);

    if ($resultado) {
        header("Location: ./admin.php?mensagem=Excluido com sucesso");
        exit();
    } else {
        header("Location: ./admin.php?mensagem=Ocorreu erro ao excluir");
        exit();
    }
} else {
    header("Location: ./admin.php?mensagem=Selecione um usuario para apagar");
    exit();
}

?>