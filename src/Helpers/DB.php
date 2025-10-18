<?php

namespace App\Helpers;

use PDO;

class DB {
    protected PDO $pdo;

    public function __construct() {
        $host = 'db'; // Alterar caso não for usar com docker
        $dbname = 'banking';
        $user = 'bank';
        $pass = 'bank123';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

}
