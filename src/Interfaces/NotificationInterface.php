<?php

namespace App\Interfaces;

interface NotificationInterface
{
    public function notify(string $message): bool;
}
