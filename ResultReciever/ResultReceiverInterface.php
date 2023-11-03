<?php

namespace App\ResultReciever;

interface ResultReceiverInterface
{
    public function receive(array $data): void;
}