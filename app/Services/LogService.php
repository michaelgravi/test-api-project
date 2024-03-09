<?php

namespace App\Services;

use App\Events\StoreTransactionEvent;
use Illuminate\Support\Facades\Log;

class LogService
{
    static function log()
    {
        Log::info(__("transaction_message.success"));
        EmailService::sendEmail(auth()->user(), []);
        event(new StoreTransactionEvent());
        return true;
    }
}
