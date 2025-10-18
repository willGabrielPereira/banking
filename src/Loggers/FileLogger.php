<?php

namespace App\Loggers;

use App\Interfaces\NotificationInterface;

class FileLogger implements NotificationInterface
{
    public function notify(string $message): bool
    {
        $logFile = '/var/www/html/log.txt';
        $formattedMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        return file_put_contents($logFile, $formattedMessage, FILE_APPEND) !== false;
    }
}
