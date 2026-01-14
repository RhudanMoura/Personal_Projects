<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>Desafio de PHP</title>
</head>

<body>
    <main>
        <h1>Data Futura</h1>

        <?php
        function AdicionarDias($dias)
        {

            $dataAtual = date("Y-m-d");

            $dataFutura = date("Y-m-d", strtotime("+$dias days"));

            echo "<p>Data atual: $dataAtual</p>";
            echo "<p>Data após $dias dias: $dataFutura</p>";
        }

        adicionarDias(10);

        ?>

</body>

</html>