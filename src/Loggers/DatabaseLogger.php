<?php

namespace App\Loggers;

use App\Interfaces\NotificationInterface;
use App\Helpers\DB;

class DatabaseLogger implements NotificationInterface
{
    public function notify(string $message): bool
    {
        $db = new DB();
        $conn = $db->getConnection();
        $stmt = $conn->prepare('INSERT INTO events (event_date, message) VALUES (NOW(), ?)');
        return $stmt->execute([$message]);
    }
}
