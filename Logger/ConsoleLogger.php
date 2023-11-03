<?php

namespace App\Logger;

class ConsoleLogger implements LoggerInterface
{

    public function write($message): void
    {
        echo $message . PHP_EOL;
    }
}