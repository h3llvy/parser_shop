<?php

namespace App\PartParsers\Property;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

class ParserOfPropertyName
{
    public function parse(RemoteWebElement $propertyGroup): string
    {
        $webDriverBy = WebDriverBy::tagName('span');
        $propertyNameElement = $propertyGroup->findElements($webDriverBy)[0];
        return trim(
            str_replace(
                ':', '', $propertyNameElement->getText()
            )
        );
    }
}