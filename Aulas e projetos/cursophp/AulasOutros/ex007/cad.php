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
        $numero = $_REQUEST["numero"];
        $dolar = 6.13;

        $conversao = $numero / $dolar;

        $conversaoFormatado = number_format($conversao, 2, ".");

        echo "O valor digitado (R$ $numero reais) convertido em dólar é $$conversaoFormatado, com a cotação atual em $$dolar";

        ?>


    </main>

</body>

</html>