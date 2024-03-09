<?php

namespace App\ExchangeRate;

use App\ExchangeRate\ExchangeRate;
use Illuminate\Support\Facades\Http;

class ExchangeRateJsonDriver extends ExchangeRate
{
    protected $request;

    public function __construct()
    {
        $this->request = $this->makeApiCall(config('services.exchange_rate.json_url'));
    }

    public function getExchangeRate()
    {
        $response = $this->request;
        $data = $response->body();
        $jsonData = json_decode($data, true);
        return $jsonData;
    }
}
