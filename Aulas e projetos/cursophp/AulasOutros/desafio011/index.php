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
    $preco = $_REQUEST["preco"] ?? "0";
    $reaj = $_REQUEST["reaj"] ?? "0";
    ?>

    <main>
        <h1>Reajustador de Preços</h1>
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">

            <!-- Preço do Produto -->
            <label for="preco">Preço do Produto (R$)</label>
            <input type="number" name="preco" id="preco" min="1" step="0.01" value="<?= $preco ?>">

            <!--Percentual de reajuste -->
            <label for="reajuste">
                Qual será o percentual de reajuste?
                (<strong><span id="p">0</span>%</strong>)
            </label>
            <input type="range" name="reaj" id="reaj" min="0" max="100" step="1" oninput="mudaValor()"
                value="<?= $reaj ?>">

            <!-- Button -->
            <input type="submit" value="Reajuste">
        </form>
    </main>
    <?php
    $aumento = $preco * $reaj / 100;
    $novo = $preco + $aumento;
    ?>



    <section>
        <h2>Resultado do reajuste</h2>
        <p>O produto que custava R$
            <?= number_format($preco, 2, ".", ",") ?>, com <?= $reaj ?>% de aumento vai passar a custar R$
            <?= number_format($novo, 2, ".", ",") ?>

            partir de
            agora.
        </p>
    </section>

    <script>
    mudaValor()

    function mudaValor() {
        p.innerText = reaj.value;
    }
    </script>
</body>

</html>