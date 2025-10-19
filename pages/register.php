<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Users;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$currentUser = Users::find($userId);

if (!$currentUser || !$currentUser->is_superuser) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$messageType = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $is_superuser = isset($_POST['is_superuser']);

    if (!empty($name)) {
        try {
            $user = new Users($name, 0, $is_superuser);
            if ($user->save()) {
                $message = "Usuário '{$name}' cadastrado com sucesso.";
                $messageType = 'success';
            } else {
                throw new \Exception("Não foi possível salvar o usuário.");
            }
        } catch (\Exception $e) {
            $message = "Erro: " . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        $message = "O nome do usuário é obrigatório.";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #007bff; }
        nav { margin-bottom: 20px; }
        nav a { text-decoration: none; color: #007bff; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="text"] { width: 95%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .form-group input[type="checkbox"] { margin-right: 5px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <nav><a href="dashboard.php">‹ Voltar para o Dashboard</a></nav>
        <h1>Cadastro de Novo Usuário</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Nome do Usuário:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_superuser" value="1">
                    É Super Usuário?
                </label>
            </div>
            <button type="submit">Cadastrar Usuário</button>
        </form>
    </div>
</body>
</html>
