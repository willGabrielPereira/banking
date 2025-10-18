<?php

use App\Helpers\Withdrawal\WithdrawalFewest;
use App\Helpers\Withdrawal\WithdrawalSaveHighValue;
use App\Helpers\Withdrawal\WithdrawalService;
use App\Loggers\DatabaseLogger;
use App\Loggers\FileLogger;
use App\Models\Inventory;

require __DIR__ . '/../vendor/autoload.php';


$message = '';
$messageType = ''; // 'success' or 'error'
$composition = null;
$suggestions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $strategyType = $_POST['strategy'] ?? 'fewest_bills';

    if ($amount > 0) {
        $withdrawalService = new WithdrawalService([
            // new DatabaseLogger(),
            new FileLogger()
        ]);

        // Seleciona a estratégia com base na escolha do usuário
        $strategy = $strategyType === 'save_high_value'
            ? new WithdrawalSaveHighValue()
            : new WithdrawalFewest();

        try {
            $composition = $withdrawalService->execute($amount, $strategy);

            $message = "Saque de R$ " . number_format($amount, 2, ',', '.') . " realizado com sucesso!";
            $messageType = 'success';
        } catch (\Exception $e) {
            $message = "Erro: " . $e->getMessage();
            $messageType = 'error';
            $inventory = Inventory::getAll();
            $suggestions = $strategy->getAlternativeAmounts($amount, $inventory);
        }
    } else {
        $message = "Erro: O valor do saque deve ser um número positivo.";
        $messageType = 'error';
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Simular Saque</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #007bff; }
        nav { margin-bottom: 20px; }
        nav a { text-decoration: none; color: #007bff; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 95%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .message { padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .composition-box { margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
        .composition-box h3 { margin-top: 0; }
        .composition-box ul { list-style-type: none; padding: 0; }
        .composition-box li { background-color: #e9ecef; margin-bottom: 5px; padding: 8px; border-radius: 4px; }
        .suggestions-box { margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f0f8ff; }
    </style>
</head>

<body>
    <div class="container">
        <nav><a href="../index.php">‹ Voltar para Home</a></nav>
        <h1>Simular Saque</h1>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($suggestions)): ?>
            <div class="suggestions-box">
                <h3>Valores alternativos para saque:</h3>
                <ul>
                    <?php foreach ($suggestions as $suggestion): ?>
                        <li>R$ <?= htmlspecialchars(number_format($suggestion, 2, ',', '.')) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($composition): ?>
            <div class="composition-box">
                <h3>Cédulas entregues:</h3>
                <ul>
                    <?php foreach ($composition as $item): ?>
                        <li><?= htmlspecialchars($item['amount']) ?> x R$ <?= htmlspecialchars(number_format($item['value'], 2, ',', '.')) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="withdraw.php" method="POST">
            <div class="form-group">
                <label for="amount">Valor do Saque (R$):</label>
                <input type="number" id="amount" name="amount" step="0.01" placeholder="Ex: 180.00" required>
            </div>
            <div class="form-group">
                <label for="strategy">Estratégia de Saque:</label>
                <select id="strategy" name="strategy">
                    <option value="fewest_bills">Padrão (Menor quantidade de cédulas)</option>
                    <option value="save_high_value">Alternativa (Preservar cédulas maiores)</option>
                </select>
            </div>
            <button type="submit">Realizar Saque</button>
        </form>
    </div>
</body>

</html>