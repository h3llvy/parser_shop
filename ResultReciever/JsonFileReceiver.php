<?php

namespace App\ResultReciever;

class JsonFileReceiver implements ResultReceiverInterface
{
    public function __construct(private readonly string $filePath = __DIR__ . '/../product.json')
    {
    }

    public function receive(array $data): void
    {
        $jsonContent = json_encode($data);
        file_put_contents($this->filePath, $jsonContent);
    }
}