<?php

namespace App\ExchangeRate;

class CombinedExchangeRateDriver
{
    protected $xmlDriver;
    protected $jsonDriver;
    protected $csvDriver;

    public function __construct()
    {
        $this->xmlDriver = new ExchangeRateXmlDriver;
        $this->jsonDriver = new ExchangeRateJsonDriver;
        $this->csvDriver = new ExchangeRateCsvDriver;
    }

    public function getExchangeRate()
    {
        // Get exchange rates from all drivers
        $exchangeRates = [
            $this->xmlDriver->getExchangeRate(),
            $this->jsonDriver->getExchangeRate(),
            $this->csvDriver->getExchangeRate(),
        ];

        return $this->getExchangeMedianRate($exchangeRates);
    }

    public function getExchangeMedianRate($exchangeRates)
    {
        $usd = 0;
        $eur = 0;
        foreach ($exchangeRates as $exchangeRate) {
            $usd += $exchangeRate['USD'];
            $eur += $exchangeRate['EUR'];
        }
        return ['EUR' => $eur / 3, 'USD' => $usd / 3];
    }
}
