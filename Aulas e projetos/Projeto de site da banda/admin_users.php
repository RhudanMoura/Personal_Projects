<?php
$result = $conexao->query("SELECT id, username, email, user_type FROM users");

echo "<h2>Gestão de Utilizadores</h2>";
echo "<table class='table table-bordered'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Tipo</th><th>Ações</th></tr>";

while ($u = $result->fetch_assoc()) {
    echo "<tr>
    <td>{$u['id']}</td>
    <td>" . htmlspecialchars($u['username']) . "</td>
    <td>" . htmlspecialchars($u['email']) . "</td>
    <td>{$u['user_type']}</td>
    <td>
      <a href='edit_user.php?id={$u['id']}' class='btn btn-sm btn-warning'>Editar</a>
      <a href='delete_user.php?id={$u['id']}' class='btn btn-sm btn-danger'>Remover</a>
    </td>
  </tr>";
}
echo "</table>";
?>