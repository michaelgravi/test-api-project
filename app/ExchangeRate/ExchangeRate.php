<?php

namespace App\ExchangeRate;

use Illuminate\Support\Facades\Http;

abstract class ExchangeRate
{
    final public function getExchangeRateData($api_url)
    {
        $this->makeApiCall($api_url);
        $this->getExchangeRate();
    }

    protected function makeApiCall($api_url)
    {
        return Http::get($api_url);
    }

    abstract public function getExchangeRate();
}
