<?php
// Obter dados do cliente
$ip = $_SERVER['REMOTE_ADDR'];
$porta = $_SERVER['REMOTE_PORT'];
$dataHora = date("Y-m-d H:i:s");
$url = $_SERVER['REQUEST_URI'];

// Criar a string que será gravada
$linha = "IP: $ip | Porta: $porta | Data e Hora: $dataHora | URL: $url\n";

// Caminho do ficheiro onde serão guardados os dados
$ficheiro = "registos.txt";

// Escrever os dados no ficheiro (modo append para não apagar os dados anteriores)
file_put_contents($ficheiro, $linha, FILE_APPEND);

// Opcional: exibir uma mensagem ao visitante
echo "<p>Os seus dados foram registados com sucesso!</p>";
?>