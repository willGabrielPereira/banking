<?php

namespace App\Helpers\Withdrawal;

use App\Interfaces\WithdrawalStrategyInterface;
use App\Models\Inventory;
use App\Models\Users;

class WithdrawalService
{
    private $notifiers = [];

    public function __construct(array $notifiers = [])
    {
        foreach ($notifiers as $notifier) {
            if ($notifier instanceof \App\Interfaces\NotificationInterface) {
                $this->notifiers[] = $notifier;
            }
        }
    }

    /**
     * Executa a operação de saque usando uma estratégia específica.
     *
     * @param float $amount O valor a ser sacado.
     * @param WithdrawalStrategyInterface $strategy A estratégia de composição de cédulas.
     * @param Users $user O usuário que está realizando o saque.
     * @return array A composição de cédulas do saque.
     * @throws \Exception Se o saque não puder ser concluído.
     */
    public function execute(float $amount, WithdrawalStrategyInterface $strategy, Users $user): array
    {
        if ($user->balance < $amount) {
            throw new \Exception("Saldo insuficiente para realizar o saque.");
        }

        $inventory = Inventory::getAll();
        $composition = $strategy->compose($amount, $inventory);

        if ($composition === null) {
            throw new \Exception("Não foi possível compor o valor de R$ " . number_format($amount, 2, ',', '.') . " com as cédulas disponíveis.");
        }

        try {
            foreach ($composition as $item) {
                Inventory::remove($item['amount'], $item['value']);
            }
        } catch (\Exception $e) {
            throw new \Exception("Ocorreu um erro ao atualizar o inventário durante o saque. Operação cancelada.");
        }

        $user->balance -= $amount;
        $user->save();

        foreach ($this->notifiers as $notifier) {
            $notifier->notify("Saque de R$ " . number_format($amount, 2, ',', '.') . " realizado com sucesso.");
        }

        return $composition;
    }
}
