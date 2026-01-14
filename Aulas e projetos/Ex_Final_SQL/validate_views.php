<?php
include 'db_connect.php';

// Lista de views para testar
$views = ['event_details', 'customer_sales_summary', 'event_sales_summary', 'tickets_status'];

foreach ($views as $view) {
    echo "<h3>🧠 Testando view: $view</h3>";

    $sql = "SELECT * FROM $view LIMIT 5"; // Mostra só os 5 primeiros resultados
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "✅ A view <b>$view</b> retornou " . $result->num_rows . " linhas.<br>";
        echo "<table border='1' cellpadding='5'><tr>";

        // Cabeçalhos
        while ($field = $result->fetch_field()) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";

        // Dados
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table><br><br>";
    } elseif ($result && $result->num_rows == 0) {
        echo "⚠️ A view <b>$view</b> está vazia.<br><br>";
    } else {
        echo "❌ Erro ao consultar <b>$view</b>: " . $conn->error . "<br><br>";
    }
}

$conn->close();
?>