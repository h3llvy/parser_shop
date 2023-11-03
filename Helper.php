<?php

namespace App;

use Closure;
use Facebook\WebDriver\Exception\NoSuchElementException;

class Helper
{
    public static function findElementOrNull(Closure $closure)
    {
        try {
            return $closure();
        } catch (NoSuchElementException $_) {
            return null;
        }
    }
}