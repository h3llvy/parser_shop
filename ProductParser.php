<?php

namespace App;

use App\CaptchaParser\AbstractCaptchaPasser;
use App\Logger\LoggerInterface;
use App\PartParsers\BaseData\ParserOfCountReviews;
use App\PartParsers\BaseData\ParserOfRating;
use App\PartParsers\BaseData\ParserOfTitle;
use App\PartParsers\Property\ParserOfNextPropertyValue;
use App\PartParsers\Property\ParserOfPropertyName;
use App\PartParsers\Property\ParserOfPropertyValue;
use App\ResultReciever\ResultReceiverInterface;
use Facebook\WebDriver\Exception\ElementClickInterceptedException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;


class ProductParser
{
    protected array $parsedData = [];

    public function __construct(
        private RemoteWebDriver         $driver,
        private AbstractCaptchaPasser   $captchaPasser,
        private LoggerInterface         $logger,
        private ResultReceiverInterface $resultReceiver,
    )
    {
    }

    public function parse($url): void
    {
        $this->parsedData = [];

        $this->driver->get($url);

        $this->passCaptchaIfExists();

        $this->parseBaseData();

        $this->setProperties($this->parsedData);

        $this->resultReceiver->receive($this->parsedData);

        $this->logger->write('Ended');
    }

    private function passCaptchaIfExists(): void
    {
        $captchaButton = Helper::findElementOrNull(
            fn() => $this->driver->findElement(WebDriverBy::cssSelector('#px-captcha'))
        );

        if (!empty($captchaButton)) {
            $this->captchaPasser->pass($captchaButton);

            $this->logger->write("Captcha was passed");
            return;
        }

        $this->logger->write("Captcha isn't exists");
    }

    private function setProperties(&$data): void
    {
        $parserOfPropertyValue = new ParserOfPropertyValue();
        $this->setDataInParserOfPropertyValue($parserOfPropertyValue);
        $data = array_merge(
            $data,
            $parserOfPropertyValue->getData()
        );
    }

    private function setDataInParserOfPropertyValue(
        ParserOfPropertyValue &$updatingParserOfPropertyValue,
                              $propertyIndex = 0
    ): void
    {
        $this->logger->write("Recursive finding group of properties, e.g. color, width, shoe size.");
        $propertyGroup = $this->findPropertyGroup($propertyIndex);


        if (empty($propertyGroup)) {
            return;
        }

        $this->logger->write("Getting data about next properties");
        list(
            $isNeedLookOverNextProperty,
            $nextPropName,
            $nextPropValue
            ) = $this->getDataAboutNextProperty(
            $this->findPropertyGroup($propertyIndex + 1)
        );


        $propertyName = (new ParserOfPropertyName())->parse($propertyGroup);

        $this->logger->write("Parsing block of properties - '{$propertyName}'");

        $propertyListElement = $propertyGroup->findElement(WebDriverBy::cssSelector('div[role="list"]'));

        /** @var RemoteWebElement[] $propertyTextElements */
        $propertyTextElements = $propertyListElement->findElements(WebDriverBy::className('w_iUH7'));

        /** @var RemoteWebElement[] $propertyButtons */
        $propertyButtons = $propertyGroup->findElements(WebDriverBy::cssSelector('button[data-testid="variant-tile-chip"]'));

        foreach ($propertyTextElements as $indexElement => $propertyTextElement) {
            $propertyButton = $propertyButtons[$indexElement];

            $parserOfPropertyValue = new ParserOfPropertyValue($propertyTextElement);
            $parserOfPropertyValue->setValue();
            $parserOfPropertyValue->setPriceIfExists();

            if ($isNeedLookOverNextProperty) {
                $this->clickButton($propertyButton);

                $this->setDataInParserOfPropertyValue($parserOfPropertyValue, $propertyIndex + 1);
            } else {
                $parserOfPropertyValue->setIsOutOfStock();
                if (!empty($nextPropName)) {
                    $parserOfPropertyValue->setCustomField($nextPropName, $nextPropValue);
                }
            }

            $updatingParserOfPropertyValue->pushToCustomField(
                $propertyName,
                $parserOfPropertyValue->getData()
            );
        }
    }

    private function clickButton(RemoteWebElement $button): void
    {
        foreach (range(1, 10) as $_)
            try {
                $button->click();

                $this->driver->wait(10)
                    ->until(
                        fn() => empty(
                        Helper::findElementOrNull(
                            fn() => empty($this->driver->findElement(
                                WebDriverBy::id('ld-spinner-pill'))
                            )
                        )
                        )
                    );
            } catch (ElementClickInterceptedException $_) {
                sleep(1);
                $this->passCaptchaIfExists();
            }
    }

    private function parseBaseData(): void
    {
        $this->parsedData = array_merge($this->parsedData, [
            'title' => (new ParserOfTitle())->parse($this->driver),
            'rating' => (new ParserOfRating())->parse($this->driver),
            'count_reviews' => (new ParserOfCountReviews())->parse($this->driver),
        ]);

        $this->logger->write("Parsed title, rating and count reviews");
    }

    private function findPropertyGroup(int $propertyIndex): ?RemoteWebElement
    {
        return Helper::findElementOrNull(
            fn() => $this
                ->driver
                ->findElement(
                    WebDriverBy::cssSelector(
                        "div[data-testid=\"variant-group-{$propertyIndex}\"]"
                    )
                )
        );
    }

    private function getDataAboutNextProperty($propertyGroup): array
    {
        $isNeedLookOverProperty = false;
        $propName = null;
        $propValue = null;

        if (!empty($propertyGroup)) {
            $nextPropertyTextElements = $propertyGroup
                ->findElement(WebDriverBy::cssSelector('div[role="list"]'))
                ->findElements(WebDriverBy::className('w_iUH7'));

            $isNeedLookOverProperty = count($nextPropertyTextElements) > 1;

            if (!$isNeedLookOverProperty) {
                $propName = (new ParserOfPropertyName())->parse($propertyGroup);
                $propValue = (new ParserOfNextPropertyValue())->parse($nextPropertyTextElements);
            }
        }

        return [
            $isNeedLookOverProperty,
            $propName,
            $propValue
        ];
    }
}