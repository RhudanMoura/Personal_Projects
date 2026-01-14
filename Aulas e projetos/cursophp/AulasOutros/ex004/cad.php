<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>

</head>

<body>
    <header>
        <h1>Resultado do processamento</h1>
    </header>
    <main>
        <?php
        $nome = $_REQUEST["nome"];
        $telemovel = $_REQUEST["telemovel"];
        $horasSemanais = $_REQUEST["horasSemanais"];
        $diasSemanais = $_REQUEST["diasSemanais"];
        $precoHora = (float) $_REQUEST["precoHora"];


        echo "O valor da semana é " . $diasSemanais * $horasSemanais * $precoHora;




        ?>


    </main>

</body>

</html>