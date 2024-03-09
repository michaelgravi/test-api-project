<?php

namespace App\ExchangeRate;

use App\ExchangeRate\ExchangeRateDriver;
use Illuminate\Support\Facades\Http;


class ExchangeRateXmlDriver extends ExchangeRate
{
    protected $request;

    public function __construct()
    {
        $this->request = $this->makeApiCall(config('services.exchange_rate.xml_url'));
    }

    public function getExchangeRate()
    {
        $response = $this->request;
        return $this->xmlFormat($response->body());
    }

    public function xmlFormat($response)
    {
        $data = $response;
        $xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }
}
