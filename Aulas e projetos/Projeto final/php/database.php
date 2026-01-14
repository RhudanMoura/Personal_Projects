<?php
// php/database.php

function getDBConnection()
{
    // Defino as credenciais do meu banco de dados local
    $host = 'localhost';
    $dbname = 'sistema_eventos';
    $username = 'root';
    $password = '';

    // Uso utf8mb4 para garantir que acentos e emojis funcionem corretamente
    $charset = 'utf8mb4';

    try {
        // Monto a string de conexão (DSN) informando o driver, host, banco e charset
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        // Configuro o comportamento do PDO
        $options = [
            // Quero que o PHP pare e me avise se der erro de SQL (Exceptions)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Quero receber os dados sempre como um array associativo (ex: $user['email'])
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Desativo a emulação para usar a segurança nativa do MySQL contra injeção
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Crio e retorno a conexão pronta para uso
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;

    } catch (PDOException $e) {
        // Se der erro na conexão, paro o script e mostro o motivo
        die("Não foi possível conectar ao banco: " . $e->getMessage());
    }
}
?>