<?php
$result = $conexao->query("SELECT * FROM products");

echo "<h2>Gestão de Produtos</h2>";
echo "<a href='add_product.php' class='btn btn-primary mb-3'>Adicionar Produto</a>";

echo "<table class='table'>";
echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Stock</th><th>Ações</th></tr>";

while ($p = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$p['id']}</td>
    <td>" . htmlspecialchars($p['name']) . "</td>
    <td>€{$p['price']}</td>
    <td>{$p['stock']}</td>
    <td>
      <a href='edit_product.php?id={$p['id']}' class='btn btn-sm btn-warning'>Editar</a>
      <a href='delete_product.php?id={$p['id']}' class='btn btn-sm btn-danger'>Remover</a>
    </td>
  </tr>";
}
echo "</table>";
?>