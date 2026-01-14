<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Altere se necessário
$password = ""; // Altere se necessário
$dbname = "portfolio_db";
// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);
// Verificação da conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Consulta para obter dados da view_projects_summary
$summary_query = "SELECT * FROM view_projects_summary";
$summary_result = $conn->query($summary_query);

// Consulta para obter dados da view_prjects_by_category
// Aqui você pode definir uma variável de categoria específica, por exemplo 'Web Development'
$category_filter = 'Web Development'; //Altere para categoria desejada
$filtered_query = $conn->prepare("SELECT * FROM view_projects_by_category WHERE category_name = ?");
$filtered_query->bind_param('s', $category_filter);
$filtered_query->execute();
$filtered_result = $filtered_query->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initialscale=1.0">
    <title>Relatórios do Portfólio</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Relatórios do Portfólio</h1>
    <h2>Resumo dos Projetos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>Categoria</th>
        </tr>
        <?php while ($row = $summary_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['project_id']; ?></td>
                <td><?php echo $row['project_title']; ?></td>
                <td><?php echo $row['project_description']; ?></td>
                <td><?php echo $row['category_name']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <h2>Projetos por Categoria: <?php echo htmlspecialchars($category_filter); ?></h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>Categoria</th>
        </tr>
        <?php while ($row = $filtered_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['project_id']; ?></td>
                <td><?php echo $row['project_title']; ?></td>
                <td><?php echo $row['project_description']; ?></td>
                <td><?php echo $row['category_name']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <?php
    // Fechamento da conexão
    $conn->close();
    ?>
</body>