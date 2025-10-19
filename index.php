<?php
require __DIR__ . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Caixa eletrônico - Login</title>
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

        .login-form input {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bem-vindo ao Caixa Eletrônico</h1>
        <p>Por favor, insira seu ID de usuário para continuar.</p>
        <form action="pages/login.php" method="post" class="login-form">
            <input type="text" name="user_id" placeholder="Seu ID de usuário" required>
            <br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html>