<?php

namespace App\BusinessLogicLayer\managers;

use Illuminate\Support\Facades\Mail;

class MailManager
{
    public function sendEmail($viewName, $parameters, $subject) {
        Mail::send($viewName, $parameters, function($message) use ($subject) {
            $message->to(\Auth::user()->email)->subject($subject);
        });
    }

    public function sendEmailToSpecificEmail($viewName, $parameters, $subject, $receiverEmail) {
        Mail::send($viewName, $parameters, function($message) use ($subject, $receiverEmail) {
            $message->to($receiverEmail)->subject($subject);
        });
    }

    public function sendEmailToSpecificEmailWithCC($viewName, $parameters, $subject, $receiverEmail, array $ccEmail) {
        Mail::send($viewName, $parameters, function($message) use ($subject, $receiverEmail, $ccEmail) {
            $message->to($receiverEmail)->subject($subject)->cc($ccEmail);
        });
    }
}