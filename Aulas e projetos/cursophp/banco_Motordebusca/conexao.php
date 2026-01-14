<?php

$host = "localhost";
$bancodedados = "carros";
$usuario = "root";
$senha = "";

$mysqli = new mysqli($host, $usuario, $senha, $bancodedados);
if ($mysqli->connect_error) {
    die("Falha na conexão com o banco de dados");
}

?>