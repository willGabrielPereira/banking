<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Users;
use App\Models\Inventory;

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
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'add':
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
                $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_FLOAT);

                if ($amount > 0 && $value > 0) {
                    Inventory::add($amount, $value);
                    $message = "Adicionado $amount nota(s) de R$ " . number_format($value, 2, ',', '.') . " com sucesso.";
                    $messageType = 'success';
                } else {
                    throw new \Exception("Quantidade e valor devem ser números positivos.");
                }
                break;

            case 'remove':
                $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
                $value = filter_input(INPUT_POST, 'value', FILTER_VALIDATE_FLOAT);

                if ($amount > 0 && $value > 0) {
                    Inventory::remove($amount, $value);
                    $message = "Removido $amount nota(s) de R$ " . number_format($value, 2, ',', '.') . " com sucesso.";
                    $messageType = 'success';
                } else {
                    throw new \Exception("Quantidade e valor devem ser números positivos.");
                }
                break;

            case 'clear':
                Inventory::clear();
                $message = "Inventário limpo com sucesso.";
                $messageType = 'success';
                break;
        }
    } catch (\Exception $e) {
        $message = "Erro: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Sempre busca o inventário atual para exibição
$items = Inventory::getAll();

$totalValue = 0;
foreach ($items as $item) {
    $totalValue += $item->value * $item->amount;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar Inventário</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1, h2 { color: #007bff; }
        nav { margin-bottom: 20px; }
        nav a { text-decoration: none; color: #007bff; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions { display: flex; gap: 20px; margin-top: 30px; }
        .form-card { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .form-card h2 { margin-top: 0; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 95%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #c82333; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>

<body>
    <div class="container">
        <nav><a href="dashboard.php">‹ Voltar para Home</a></nav>
        <h1>Gerenciar Inventário</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <h2>Inventário Atual</h2>
        <table>
            <thead>
                <tr>
                    <th>Valor da Nota (R$)</th>
                    <th>Quantidade</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">O inventário está vazio.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars(number_format($item->value, 2, ',', '.')) ?></td>
                            <td><?= htmlspecialchars($item->amount) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p><strong>Valor Total em Caixa:</strong> R$ <?= htmlspecialchars(number_format($totalValue, 2, ',', '.')) ?></p>

        <div class="actions">
            <div class="form-card">
                <h2>Adicionar / Remover</h2>
                <form action="inventory.php" method="POST">
                    <div class="form-group">
                        <label for="value">Valor da Nota:</label>
                        <input type="number" id="value" name="value" step="0.01" placeholder="Ex: 100.00" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Quantidade:</label>
                        <input type="number" id="amount" name="amount" min="1" placeholder="Ex: 10" required>
                    </div>
                    <button type="submit" name="action" value="add">Adicionar</button>
                    <button type="submit" name="action" value="remove" class="btn-danger">Remover</button>
                </form>
            </div>

            <div class="form-card">
                <h2>Limpar Inventário</h2>
                <p>Esta ação removerá todas as notas do inventário. Não pode ser desfeita.</p>
                <form action="inventory.php" method="POST" onsubmit="return confirm('Tem certeza que deseja limpar todo o inventário?');">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn-danger">Limpar Tudo</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>