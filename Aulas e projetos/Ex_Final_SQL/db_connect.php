<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exerc_bd_final"; // o nome do teu banco

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("❌ Erro de conexão: " . $conn->connect_error);
} else {
    echo "✅ Conexão bem-sucedida!<br>";
}
?>