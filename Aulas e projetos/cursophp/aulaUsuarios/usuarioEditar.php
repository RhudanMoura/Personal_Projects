<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
<?php

if (isset($_POST) && !empty($_POST)) {
    $id = $_POST["id"];
    $nome = $_POST["Nome"];
    $login = $_POST["Login"];
    $ativo = $_POST["Ativo"] == "on" ? true : false;

    include "./conexao.php";
    $query = "update usuarios set nome = '$nome', login = '$login',ativo = $ativo where id  = $id";
    $resultado = mysqli_query($conexao, $query);
    header("Location: ./usuarios.php?mensagem=Usuario editado com sucesso");
    exit();
} else if (isset($_GET["id"]) && !empty($_GET["id"])) {
    include "./conexao.php";

    $query = "select * from usuarios where id = " . $_GET["id"];

    $resultado = mysqli_query($conexao, $query);

    $dados = mysqli_fetch_array($resultado);

    $id = $dados["id"];
    $nome = $dados["nome"];
    $login = $dados["login"];
    $ativo = $dados["ativo"];

} else {
    header("Location: ./usuarios.php?mensagem=Selecione um usuario para editar");
    exit();
}

?>

<div class="card">


    <div class="card-header">
        <h3>Editar usuário</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4 offset-4">
                <form action="usuarioEditar.php" method="post">

                    <div class="form-group">
                        <label>Id</label>
                        <input type="text" value="<?php echo $id; ?>" name="id" readonly class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" value="<?php echo $nome; ?>" name="Nome" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Login</label>
                        <input type="text" value="<?php echo $login; ?>" name="Login" class="form-control">
                    </div>


                    <div class="form-group">
                        <label>Ativo</label>
                        <?php if ($ativo == 1) {
                            ?>
                        <input type="checkbox" name="Ativo" checked class="form-check">
                        <?php
                        } else {
                            ?>
                        <input type="checkbox" name="Ativo" class="form-check">
                        <?php
                        }
                        ?>

                    </div>

                    <button class="btn btn-success" type="submit">
                        Atualizar usuário
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>