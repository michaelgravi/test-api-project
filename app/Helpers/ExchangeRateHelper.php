<?php

use App\Services\ExchangeRateService;

if (!function_exists('exchangeRate')) {
    function exchangeRate()
    {
        return new ExchangeRateService;
    }
}
