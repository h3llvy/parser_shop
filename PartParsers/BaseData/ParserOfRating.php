<?php

namespace App\PartParsers\BaseData;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class ParserOfRating
{
    public function parse(RemoteWebDriver $driver): float
    {
        $webDriverBy = WebDriverBy::className('rating-number');
        $element = $driver->findElement($webDriverBy);
        $text = $element->getText();
        return floatval(
            str_replace(['(', ')'], '', $text)
        );
    }
}