<?php
$xml = simplexml_load_file('dados.xml');
$dado = $xml->aluno->nome;

//para mudar o nome do aluno, é só passar o número de indice do array, ex:
//$dado = $xml->aluno[1]->nome;
//$dado = $xml->aluno[2]->nome;


echo $dado;
?>