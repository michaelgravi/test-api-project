<?php

namespace App\ExchangeRate;

use App\ExchangeRate\ExchangeRate;
use Illuminate\Support\Facades\Http;

class ExchangeRateCsvDriver extends ExchangeRate
{
    protected $request;

    public function __construct()
    {
        $this->request = $this->makeApiCall(config('services.exchange_rate.csv_url'));
        $this->getExchangeRate();
    }

    public function getExchangeRate()
    {
        $response = $this->request;
        $csvArray = array_map('str_getcsv', explode("\n", $response->body()));
        return ['EUR' => $csvArray[0][1], 'USD' => $csvArray[1][1]];
    }
}
