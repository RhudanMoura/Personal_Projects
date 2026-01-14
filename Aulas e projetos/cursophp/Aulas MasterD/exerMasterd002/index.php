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
        public $nome;
        public $ganhos;
        public $perdidos;
        public $qualidade;
        public function lerNome()
        {
            echo $this->nome;
        }
        public function lerGanhos()
        {
            echo $this->ganhos;
        }
        public function perdidos()
        {
            echo $this->perdidos;
        }
        public function qualidade()
        {
            echo $this->qualidade;
        }
        public function somarVitoria()
        {
            echo $this->ganhos += 1;
        }
        public function somarDerrota()
        {
            echo $this->perdidos += 1;
        }
    }

    $equipa1 = new equipa();
    $equipa1->nome = 'Lisboa';
    $equipa1->ganhos = 7;
    $equipa1->perdidos = 6;
    $equipa1->qualidade = ($equipa1->ganhos + $equipa1->perdidos) / 100;

    $equipa2 = new equipa();
    $equipa2->nome = 'Porto';
    $equipa2->ganhos = 3;
    $equipa2->perdidos = 7;
    $equipa2->qualidade = ($equipa2->ganhos + $equipa2->perdidos) / 100;




    ?>
    <p>
        <?php $equipa1->lerNome(); ?> com
        <?php $equipa1->lerGanhos(); ?> jogos ganhos e <?php $equipa1->perdidos()
               ?> perdido, tem uma qualidade média de <?php $equipa1->qualidade() ?>
    </p>
    <p>
        <?php $equipa2->lerNome(); ?> com
        <?php $equipa2->lerGanhos(); ?> e <?php $equipa2->perdidos()
               ?> tem uma qualidade média de <?php $equipa2->qualidade() ?>
    </p>

</body>

</html>