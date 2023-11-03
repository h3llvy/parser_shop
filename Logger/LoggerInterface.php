<?php

namespace App\Logger;

interface LoggerInterface
{
    public function write($message): void;
}