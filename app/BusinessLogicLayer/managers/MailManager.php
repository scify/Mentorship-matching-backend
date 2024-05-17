<?php

namespace App\BusinessLogicLayer\managers;

use Illuminate\Support\Facades\Mail;

class MailManager
{
    public static function SendEmail($viewName, $parameters, $subject, $receiverEmail) {
        Mail::send($viewName, $parameters, function($message) use ($subject, $receiverEmail) {
            $message->to($receiverEmail)->subject($subject);
        });
    }
}