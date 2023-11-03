<?php

namespace App\CaptchaParser;

use Facebook\WebDriver\Remote\RemoteWebElement;

class UserCaptchaPasser extends AbstractCaptchaPasser
{
    public function pass(RemoteWebElement $captchaButton): void
    {
        readline('Pass captcha and write anything in terminal');
    }
}