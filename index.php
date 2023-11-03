<?php

use App\Browser;

require 'vendor/autoload.php';

$chromiumUrl = 'http://localhost:5555';

$url = 'https://www.walmart.com/ip/Time-and-Tru-Women-s-Striped-Ribbed-Turtleneck-Sizes-XS-XXXL/1282901752';
$url = 'https://www.walmart.com/ip/Time-and-Tru-Women-s-Genuine-Suede-Boots-Wide-Width-Available/1218023934';

$driver = \App\WebDriverFactory::create($chromiumUrl);

$parser = new \App\ProductParser(
    driver: $driver,
    captchaPasser: new \App\CaptchaParser\UserCaptchaPasser($driver),
    logger: new \App\Logger\ConsoleLogger(),
    resultReceiver: new \App\ResultReciever\JsonFileReceiver(),
);

$parser->parse($url);