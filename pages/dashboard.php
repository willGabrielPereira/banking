<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Users;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$user = Users::find($userId);

if (!$user) {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Caixa eletrônico - Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            color: #333;
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: #007bff;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($user->name); ?></h1>
        <nav>
            <?php
                if ($user->is_superuser) {
                    echo '<a href="inventory.php">Inventário</a> | ';
                    echo '<a href="register.php">Cadastrar Usuário</a> | ';
                }
            ?>
            <a href="withdraw.php">Saque</a> | 
            <a href="logout.php">Sair</a>
        </nav>
    </div>
</body>

</html>