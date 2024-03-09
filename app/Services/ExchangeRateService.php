<?php

namespace App\Services;

use App\ExchangeRate\CombinedExchangeRateDriver;
use App\ExchangeRate\ExchangeRateCsvDriver;
use App\ExchangeRate\ExchangeRateJsonDriver;
use App\ExchangeRate\ExchangeRateXmlDriver;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{

    public function get()
    {
        $driver = strtolower(config('services.exchange_rate.driver'));
        switch ($driver) {
            case 'xml':
                $data = $this->registerXmlDriver();
                break;
            case 'json':
                $data = $this->registerJsonDriver();
                break;
            case 'csv':
                $data = $this->registerCsvDriver();
                break;
            case 'avarage':
                $data = $this->registerCombinedDriver();
                break;
        }
        return $data;
    }

    static function cacheExchangeRate()
    {
        $data = exchangeRate()->get();
        Cache::put('USD', $data["USD"]);
        Cache::put('EUR', $data["EUR"]);
        return true;
    }

    static function getCacheExchangeRate()
    {
        $data = [
            'USD' => Cache::get('USD'),
            'EUR' => Cache::get('EUR')
        ];
        return $data;
    }

    static function USDConvert($amount)
    {
        $usd = exchangeRate()->getCacheExchangeRate()['USD'];
        $amount *= $usd;
        return $amount;
    }

    protected function registerXmlDriver()
    {
        $data = new ExchangeRateXmlDriver();
        return $data->getExchangeRate();
    }

    protected function registerJsonDriver()
    {
        $data = new ExchangeRateJsonDriver();
        return $data->getExchangeRate();
    }

    protected function registerCsvDriver()
    {
        $data = new ExchangeRateCsvDriver();
        return $data->getExchangeRate();
    }

    protected function registerCombinedDriver()
    {
        $data = new CombinedExchangeRateDriver();
        return $data->getExchangeRate();
    }
}
