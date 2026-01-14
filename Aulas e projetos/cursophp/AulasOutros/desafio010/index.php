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
    $atual = date("Y");
    $nascimento = $_REQUEST["nascimento"] ?? 1998;
    $dataAtual = $_REQUEST["dataAtual"] ?? $atual;

    ?>
    <main>
        <h1>Calculando a sua idade</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">

            <!-- Ano de nascimento -->
            <label for="nascimento">Em que ano você nasceu?</label>
            <input type="number" name="nascimento" id="nascimento" min="1900" value="<?= $nascimento ?>">

            <!--Ano futuro -->
            <label for="dataAtual">
                <p>
                    Quer saber sua idade em que ano? atualmente estamos em <strong>
                        <?= $atual ?>
                    </strong>.
                </p>
            </label>
            <input type="number" name="dataAtual" id="dataAtual" value="<?= $data ?>">

            <!-- Button -->
            <input type="submit" value="Qual será minha idade?">
        </form>
    </main>
    <section>
        <h2>Resultado Final</h2>
        <?php

        $resultado = $dataAtual - $nascimento;

        if (($nascimento < 1900) || ($nascimento > 2024)) {
            echo 'Não é possível calcular nascimento abaixo de 1900 ou acima de 2024';
        } elseif (($dataAtual <= 2024) || ($dataAtual >= 2100)) {
            echo 'Não é possível calcular número abaixo de 2025 ou acima de 2099';
        } else {
            echo "<p> Quem nasceu em $nascimento vai ter $resultado anos de idade em $dataAtual !!</p>";
        }



        ?>
    </section>
</body>

</html>