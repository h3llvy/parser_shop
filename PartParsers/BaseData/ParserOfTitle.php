<?php

namespace App\PartParsers\BaseData;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class ParserOfTitle
{
    public function parse(RemoteWebDriver $driver): string
    {
        foreach (range(1, 10) as $_) {
            try {
                $webDriverBy = WebDriverBy::tagName('h1');
                $element = $driver->findElement($webDriverBy);
                return $element->getText();
            } catch (NoSuchElementException $e_) {
                usleep(200000);
            }
        }
    }
}