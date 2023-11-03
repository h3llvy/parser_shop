<?php

namespace App\PartParsers\BaseData;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class ParserOfCountReviews
{
    public function parse(RemoteWebDriver $driver): int
    {
        $webDriverBy = WebDriverBy::cssSelector('a[link-identifier="reviewsLink"]');
        $element = $driver->findElement($webDriverBy);
        $text = $element->getText();
        preg_match('/\d+/', $text, $matches);
        return intval(@$matches[0]);
    }
}