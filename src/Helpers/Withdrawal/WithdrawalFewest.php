<?php

namespace App\Helpers\Withdrawal;

use App\Interfaces\WithdrawalStrategyInterface;

/**
 * Estratégia que busca compor o saque com a menor quantidade possível de cédulas,
 * priorizando as de maior valor.
 */
class WithdrawalFewest implements WithdrawalStrategyInterface
{
    public function compose(float $amount, array $inventory): ?array
    {
        $remainingAmount = $amount;
        $composition = [];

        // O inventário deve estar ordenado do maior para o menor valor
        foreach ($inventory as $bill) {
            if ($remainingAmount <= 0) break;
            if ($bill->value > $remainingAmount || $bill->amount <= 0) continue;

            $neededBills = floor($remainingAmount / $bill->value);
            $billsToUse = min($neededBills, $bill->amount);

            if ($billsToUse > 0) {
                $composition[] = ['value' => $bill->value, 'amount' => (int)$billsToUse];
                $remainingAmount -= $billsToUse * $bill->value;
                $remainingAmount = round($remainingAmount, 2);
            }
        }

        return $remainingAmount == 0 ? $composition : null;
    }
}
