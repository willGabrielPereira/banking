<?php

namespace App\Models;

use App\Helpers\DB;

class Inventory extends DB
{
    private ?int $id = null;
    public ?int $amount = null;
    public ?float $value = null;

    private const VALID_VALUES = [200.00, 100.00, 50.00, 20.00, 10.00, 5.00, 2.00, 1.00];


    public function __construct(?int $amount = null, ?float $value = null, ?int $id = null)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->value = $value;

        if ($value && !self::isValidValue($value)) {
            throw new \Exception("O valor R$ " . number_format($value, 2, ',', '.') . " não é uma cédula ou moeda válida.");
        }

        parent::__construct();
    }

    private function insert(\PDO $conn) {
        $stmt = $conn->prepare("INSERT INTO inventory (amount, value) VALUES (:amount, :value)");
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':value', $this->value);

        return $stmt;
    }

    private function update(\PDO $conn){
        $stmt = $conn->prepare("UPDATE inventory SET amount = :amount, value = :value WHERE id = :id");
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':value', $this->value);
        $stmt->bindParam(':id', $this->id);

        return $stmt;
    }


    public function save(): bool {
        $conn = $this->getConnection();
        $stmt = null;

        $stmt = $this->id === null ? $this->insert($conn) : $this->update($conn);

        return $stmt->execute();
    }

    /**
     * Adicionar quantidade de notas disponíveis, caso não exista essa nota disponível, cria uma nova entrada
     * @param int $amount
     * @param float $value
     * @return bool
     */
    public static function add(int $amount, float $value): bool {
        $inventory = self::find($value);

        if (!$inventory) {
            $inventory = new Inventory($amount, $value);
        } else {
            $inventory->amount += $amount;
        }

        return $inventory->save();
    }

    /**
     * Remove quantidade de notas das notas disponíveis no sistema. Se não tiver notas suficientes, retorna erro
     * @param int $amount
     * @param float $value
     * @throws \Exception
     * @return bool
     */
    public static function remove(int $amount, float $value) {
        $inventory = self::find($value);

        if (!$inventory) {
            throw new \Exception("Não existe essa nota disponível no inventário");
        } else {
            $inventory->amount -= $amount;

            if ($inventory->amount < 0) {
                throw new \Exception("Quantidade insuficiente de notas disponíveis no inventário");
            }
        }

        return $inventory->save();
    }

    /**
     * Remove todas as notas do sistema
     * @return bool
     */
    public static function clear() {
        $inventory = new Inventory();
        $conn = $inventory->getConnection();
        $stmt = $conn->query("DELETE FROM inventory");

        return $stmt->execute();
    }

    /**
     * Captura todo o inventário de notas disponíveis
     *
     * @return Inventory[]
     */
    public static function getAll() {
        $inventory = new Inventory();
        $conn = $inventory->getConnection();
        $stmt = $conn->query("SELECT * FROM inventory order by value desc");

        $results = $stmt->fetchAll();

        $toReturn = [];
        foreach ($results as $value) {
            $toReturn[] = new Inventory($value['amount'], $value['value'], $value['id']);
        }

        return $toReturn;
    }

    /**
     * Encontra uma nota específica no inventário
     *
     * @param float $value
     * @return Inventory|null
     */
    public static function find(float $value) {
        $inventory = new Inventory();
        $conn = $inventory->getConnection();
        $stmt = $conn->prepare("SELECT * FROM inventory WHERE value = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        $results = $stmt->fetch();

        if ($results) {
            return new Inventory($results['amount'], $results['value'], $results['id']);
        }

        return null;
    }

    /**
     * Verifica se o valor monetário corresponde a uma cédula ou moeda válida.
     *
     * @param float $value
     * @return boolean
     */
    private static function isValidValue(float $value): bool
    {
        return in_array($value, self::VALID_VALUES, true);
    }

    public function __toString(): string
    {
        return "Inventory - Amount: {$this->amount}, Value: R$ {$this->value}" . ($this->id ? ", ID: {$this->id}" : "");
    }
}
