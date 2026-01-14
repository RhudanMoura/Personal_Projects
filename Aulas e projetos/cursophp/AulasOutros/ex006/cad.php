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
        $numero = $_REQUEST["numeroDigitado"];

        $antecessor = $numero - 1;
        $sucessor = $numero + 1;



        echo "O número antecessor de $numero é $antecessor e o número sucessor é $sucessor";




        ?>


    </main>

</body>

</html>