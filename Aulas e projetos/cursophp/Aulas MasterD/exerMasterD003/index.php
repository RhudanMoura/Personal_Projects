<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    class equipa
    {
        public $name = "";
        public $pontos = 0;
        public $vitoria = 0;
        public $empate = 0;
        public $derrota = 0;

        public $valorVitoria = 3;
        public $valorEmpate = 2;
        public $valorDerrota = 1;

        function lerNome()
        {
            echo $this->name;
        }
        function lerPontos()
        {
            echo $this->pontos;
        }
        function inserirPont($v, $e, $d)
        {
            $this->vitoria = $v;
            $this->empate = $e;
            $this->derrota = $d;

            $this->pontos = ($this->vitoria * $this->valorVitoria) + ($this->empate * $this->valorEmpate) + ($this->derrota * $this->valorDerrota);
        }
    }
    $equipa = new equipa();
    $equipa->name = "Lisboa";


    $equipaRegional = new equipa();
    $equipaRegional->name = "Lisboa B";

    $equipaRegional->valorVitoria = 4;
    $equipaRegional->valorEmpate = 1;
    $equipaRegional->valorDerrota = 0;

    $equipa->inserirPont(1, 0, 0);
    $equipaRegional->inserirPont(1, 0, 0);

    echo "Equipa: ";
    $equipa->lerNome();
    echo " <br>Pontos: ";
    $equipa->lerPontos();

    echo "<br>Equipa Regional: ";
    $equipaRegional->lerNome();
    echo " <br>Pontos: ";
    $equipaRegional->lerPontos();
    ?>

</body>

</html>