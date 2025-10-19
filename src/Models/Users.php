<?php

namespace App\Models;

use App\Helpers\DB;

class Users extends DB
{
    private ?int $id = null;
    public string $name;
    public float $balance;
    public bool $is_superuser;

    public function __construct(string $name = '', float $balance = 0.00, bool $is_superuser = false, ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->balance = $balance;
        $this->is_superuser = $is_superuser;

        parent::__construct();
    }

    private function insert(\PDO $conn) {
        $stmt = $conn->prepare("INSERT INTO users (name, balance, is_superuser) VALUES (:name, :balance, :is_superuser)");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':balance', $this->balance);
        $stmt->bindParam(':is_superuser', $this->is_superuser, \PDO::PARAM_BOOL);

        return $stmt;
    }

    private function update(\PDO $conn){
        $stmt = $conn->prepare("UPDATE users SET name = :name, balance = :balance, is_superuser = :is_superuser WHERE id = :id");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':balance', $this->balance);
        $stmt->bindParam(':is_superuser', $this->is_superuser, \PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $this->id);

        return $stmt;
    }

    public function save(): bool {
        $conn = $this->getConnection();
        $stmt = null;

        $stmt = $this->id === null ? $this->insert($conn) : $this->update($conn);

        return $stmt->execute();
    }

    public static function find(int $id): ?Users
    {
        $user = new Users();
        $conn = $user->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result) {
            return new Users($result['name'], $result['balance'], $result['is_superuser'], $result['id']);
        }

        return null;
    }
}