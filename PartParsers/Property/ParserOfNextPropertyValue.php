<?php

namespace App\PartParsers\Property;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

class ParserOfNextPropertyValue
{
    /**
     * @param RemoteWebElement[] $nextPropertyTextElements
     * @return string
     */
    public function parse(array $nextPropertyTextElements): string
    {
        $explodedText = explode(',', $nextPropertyTextElements[0]->getText());

        if (trim(@$explodedText[0]) === 'selected') {
            array_shift($explodedText);
        }
        return trim(@$explodedText[0]);
    }
}