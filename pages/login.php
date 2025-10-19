<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Users;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    if (empty($userId) || !is_numeric($userId)) {
        die('ID de usuário inválido.');
    }

    $user = Users::find((int)$userId);

    if ($user) {
        $_SESSION['user_id'] = $userId;
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Usuário não encontrado.";
    }
}