<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Services
    |--------------------------------------------------------------------------
    | This configuration pertains to the exchange rate service. It comprises three
    | individual drivers, along with a combined driver. When making a request,
    | the driver needs to specify parameters, which can be: XML, JSON, CSV and average.
    |
    */
    'exchange_rate' => [
        'driver' => env('EXCHANGE_RATE_DRIVER',"avarage"),
        'json_url'=>env('EXCHANGE_RATE_JSON_URL'),
        'csv_url'=>env('EXCHANGE_RATE_CSV_URL'),
        'xml_url'=>env('EXCHANGE_RATE_XML_URL'),

    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
