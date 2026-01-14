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
    $minimo = 1_380.60;
    $salario = $_REQUEST['sal'] ?? 0;

    ?>
    <main>
        <h1>Anatomia de uma divisão</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
            <label for="sal">Salário</label>
            <input type="number" name="sal" id="sal" value="<?= $salario ?>" step="0.01">
            <p>Considerando o salário mínimo de
                <strong>R$<?= number_format($minimo, 2, ",", ".") ?></strong>
            </p>
            <input type="submit" value="Calcular">
        </form>

        <h2>Resultado final</h2>

        <?php

        $quantidade = intdiv($salario, $minimo);
        $dif = $salario % $minimo;

        echo "<p>Ganha $quantidade salários mínimos + R\$ " . number_format($dif, 2, ",", ".") . ". </p>";
        ?>



    </main>
</body>

</html>