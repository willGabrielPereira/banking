<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Projeto PHP com Docker</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; color: #333; text-align: center; margin-top: 50px; }
        h1 { color: #007bff; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .php-version { font-weight: bold; }
        .db-status { margin-top: 15px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Olá, Mundo! Meu ambiente Docker está no ar!</h1>
        <p>Se você está vendo esta página, o Nginx e o PHP estão funcionando corretamente.</p>
        <p>Versão do PHP: <span class="php-version"><?php echo phpversion(); ?></span></p>

        <div class="db-status">
            <?php
            // Detalhes da conexão com o banco de dados (devem ser os mesmos do docker-compose.yml)
            $host = 'db'; // O nome do serviço do banco de dados no docker-compose
            $dbname = 'banking';
            $user = 'bank';
            $pass = 'bank123';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                // A extensão pdo_mysql precisa estar instalada no Dockerfile do PHP
                $pdo = new PDO($dsn, $user, $pass, $options);
                echo '<p class="success">Conexão com o banco de dados MariaDB foi bem-sucedida!</p>';
            } catch (\PDOException $e) {
                echo '<p class="error">Falha na conexão com o banco de dados: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
