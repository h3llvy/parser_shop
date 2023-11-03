<?php

namespace App\CaptchaParser;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\RemoteWebElement;

class SeleniumCaptchaPasser extends AbstractCaptchaPasser
{

    public function pass(RemoteWebElement $captchaButton): void
    {
        sleep(2);

        $actions = new WebDriverActions($this->driver);
        $actions->clickAndHold($captchaButton)
            ->perform();

        sleep(11);

        $actions->release($captchaButton)
            ->perform();

        usleep(200000 - rand(100, 10000));

        $actions->release($captchaButton)
            ->perform();
    }
}