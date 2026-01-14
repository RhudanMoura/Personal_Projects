<?php
include 'db_connect.php';

// Lista de tabelas para validar
$tables = ['events', 'tickets', 'customers', 'sales'];

foreach ($tables as $table) {
    echo "<h3>🔍 Verificando tabela: $table</h3>";

    $result = $conn->query("SELECT COUNT(*) AS total FROM $table");

    if ($result) {
        $row = $result->fetch_assoc();
        echo "✔️ Tabela <b>$table</b> contém <b>" . $row['total'] . "</b> registros.<br><br>";
    } else {
        echo "❌ Erro ao consultar $table: " . $conn->error . "<br><br>";
    }
}

$conn->close();
?>