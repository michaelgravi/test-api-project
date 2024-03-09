<?php

namespace App\Services;

use App\Mail\MailTransactionStore;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    static function sendEmail($recipientEmail, $data)
    {
        $recipientEmail = $recipientEmail->email;
        Mail::to($recipientEmail)->send(new MailTransactionStore($data));
    }
}
