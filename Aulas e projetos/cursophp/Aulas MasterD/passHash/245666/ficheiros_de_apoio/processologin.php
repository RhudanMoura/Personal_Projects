<html>

<head>
    <meta charset="UTF-8">
    <title>Login - Prática 31</title>
    <link href="estilos.css" rel="stylesheet">
</head>

<body>

    <div class="caixa0">
        <span id="logo"><img src="logo.png"></span>
    </div>

    <?php

    //Aqui deverá ser escrito o código php
    
    $senha = $_REQUEST["pwd"];;
    $senhaPass = password_hash($senha, PASSWORD_DEFAULT);
    $senhaHash = "$2y$10$.Rz2DbWRFjXNAo99fEJ5gOMWmqkn2/45kvKbCiAibTXMWQJmHo.42";

    if (password_verify($senha, $senhaHash)) {
            echo '<div class="caixa1"><h2>LOGIN COM SUCESSO</h2></div>';
    } else {
        echo '<div class="caixa1"><h2>Senha incorreta!</h2></div>';
    }
    ?>


</body>

</html>