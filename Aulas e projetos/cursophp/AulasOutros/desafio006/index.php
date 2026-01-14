<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>Desafio de PHP</title>
</head>

<body>
    <?php
    $dividendo = $_REQUEST['d1'] ?? 0;
    $divisor = $_REQUEST['d2'] ?? 1;

    ?>
    <main>
        <h1>Anatomia de uma divisão</h1>
        <form action="" method="get">
            <label for="d1">Dividendo</label>
            <input type="number" name="d1" id="d1" value="<?= $dividendo ?>">
            <label for="d2">Divisor</label>
            <input type="number" name="d2" id="d2" value="<?= $divisor ?>">
            <input type="submit" value="Analisar">

        </form>
        <section>
            <h2>Estrutura de divisão</h2>
            <?php

            //Cálculos
            $quociente = intdiv($dividendo, $divisor);
            $resto = $dividendo % $divisor;
            echo "<ul>";
            echo "<li>Dividendo: $dividendo</li>";
            echo "<li>Divisor: $divisor</li>";
            echo "<li>Quociente: $quociente</li>";
            echo "<li>Resto: $resto</li>";
            echo "</ul>";
            ?>
        </section>
    </main>
</body>

</html>