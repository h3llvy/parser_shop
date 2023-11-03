<?php

namespace App\PartParsers\Property;

use Facebook\WebDriver\Remote\RemoteWebElement;
use LogicException;

class ParserOfPropertyValue
{
    private array $explodedText;
    private array $data = [];

    public function __construct(RemoteWebElement $propertyTextElement = null)
    {
        if (empty($propertyTextElement)) {
            return;
        }

        $this->explodedText = explode(',', $propertyTextElement->getText());
        if (trim(@$this->explodedText[0]) === 'selected') {
            array_shift($this->explodedText);
        }
    }

    public function setValue(): void
    {
        if (empty($this->explodedText)) {
            throw new LogicException();
        }

        $this->data['value'] = trim($this->explodedText[0]);
    }

    public function setPriceIfExists(): void
    {
        if (empty($this->explodedText)) {
            throw new LogicException();
        }

        if (empty($this->explodedText[1])) {
            return;
        }

        preg_match('/(\d+\.\d+)/', $this->explodedText[1], $matchesWithPoint);
        preg_match('/(\d+)/', $this->explodedText[1], $matchesWithoutPoint);
        $price = max(@$matchesWithPoint[0], @$matchesWithoutPoint[0]);
        if (!empty($price)) {
            $this->data['price'] = floatval($price);
        }
    }

    public function setIsOutOfStock(): void
    {
        if (empty($this->explodedText)) {
            throw new LogicException();
        }

        $this->data['is_out_of_stock'] = str_contains(
            strtolower(end($this->explodedText))
            , 'out of stock'
        );
    }

    public function setCustomField(string $key, mixed $nextPropValue)
    {
        $this->data[$key] = $nextPropValue;
    }

    public function pushToCustomField(string $key, mixed $nextPropValue)
    {
        $this->data[$key][] = $nextPropValue;
    }

    public function getData(): array
    {
        return $this->data;
    }
}