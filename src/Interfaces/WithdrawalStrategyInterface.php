<?php

namespace App\Interfaces;

interface WithdrawalStrategyInterface
{
    /**
     * Calcula a composição de cédulas para um determinado valor de saque com base no inventário disponível.
     *
     * @param float $amount O valor a ser sacado.
     * @param \App\Models\Inventory[] $inventory O inventário de cédulas disponíveis.
     * @return array|null Um array representando as cédulas e suas quantidades para o saque, ou null se a composição não for possível.
     */
    public function compose(float $amount, array $inventory): ?array;
}
