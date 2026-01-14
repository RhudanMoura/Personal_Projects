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
    $saque = $_REQUEST["saque"] ?? 0;
    ?>


    <main>
        <h1>Caixa Eletrônico</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">

            <!-- Valor a sacar -->
            <label for="preco">Qual valor que você deseja sacar? (R$) <sup>*</sup></label>
            <input type="number" name="saque" id="saque" step="5" required value="<?= $saque ?>">

            <!--Notas disponíveis -->
            <p style="font-size: 0.8em;"><sup>*</sup>Notas disponíveis: R$100, R$ 50, R$10 e R$5</p>

            <!-- Button -->
            <input type="submit" value="Sacar">
        </form>
    </main>

    <?php

    $resto = $saque;
    //Saque de R$100
    $tot100 = (int) ($resto / 100);
    $resto = $resto % 100; // $resto %=100;
    
    //Saque de R$50
    $tot50 = (int) ($resto / 50);
    $resto = $resto % 50; // $resto %=50;
    
    //Saque de R$10
    $tot10 = (int) ($resto / 10);
    $resto = $resto % 10; // $resto %=10;
    
    //Saque de R$5
    $tot5 = (int) ($resto / 5);
    $resto = $resto % 5; // $resto %=5;
    ?>

    <section>

        <h2>Saque de R$[??] realizado</h2>
        <p>O caixa eletrônico vai te entrega as seguintes notas:</p>
        <ul>
            <li>100 <p>(<?= $tot100 ?> notas de 100 reais)</p>
            </li>
            <li>50 <p>(<?= $tot50 ?> notas de 50 reais)</p>
            </li>
            <li>10 <p>(<?= $tot10 ?> notas de 10 reais)</p>
            </li>
            <li>5 <p>(<?= $tot5 ?> notas de 5 reais)</p>
            </li>
        </ul>

    </section>

</body>

</html>