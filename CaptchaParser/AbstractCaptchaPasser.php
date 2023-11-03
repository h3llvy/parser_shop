<?php

namespace App\CaptchaParser;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;

abstract class AbstractCaptchaPasser
{
    public function __construct(protected RemoteWebDriver $driver)
    {
    }

    abstract public function pass(RemoteWebElement $captchaButton): void;
}