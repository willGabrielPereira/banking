<?php

namespace App\Helpers\Withdrawal;

use App\Interfaces\WithdrawalStrategyInterface;
use App\Models\Inventory;

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
     * @return array A composição de cédulas do saque.
     * @throws \Exception Se o saque não puder ser concluído.
     */
    public function execute(float $amount, WithdrawalStrategyInterface $strategy): array
    {
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

        foreach ($this->notifiers as $notifier) {
            $notifier->notify("Saque de R$ " . number_format($amount, 2, ',', '.') . " realizado com sucesso.");
        }

        return $composition;
    }
}
