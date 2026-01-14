<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <main>
        <?php
        $vector = array("laranja", "azul", "verde", "amarelo", "vermelho", "roxo");
        foreach ($vector as $cor) {
            echo "Cor: $cor <br>";
        }
        ?>
    </main>

</body>

</html>