<?php
$result = $conexao->query("
  SELECT o.id, o.total, o.status, o.created_at, u.username 
  FROM orders o 
  LEFT JOIN users u ON o.user_id = u.id
");

echo "<h2>Gestão de Encomendas</h2>";
echo "<table class='table'>";
echo "<tr><th>ID</th><th>Utilizador</th><th>Total</th><th>Status</th><th>Data</th><th>Ações</th></tr>";

while ($o = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$o['id']}</td>
    <td>{$o['username']}</td>
    <td>€{$o['total']}</td>
    <td>{$o['status']}</td>
    <td>{$o['created_at']}</td>
    <td>
      <a href='order_details.php?id={$o['id']}' class='btn btn-sm btn-info'>Detalhes</a>
      <a href='process_order.php?id={$o['id']}' class='btn btn-sm btn-success'>Marcar Processada</a>
    </td>
  </tr>";
}
echo "</table>";
?>