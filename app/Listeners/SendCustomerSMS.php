<?php

namespace App\Listeners;

use App\Classes\SMS;
use App\Events\SendSMS;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomerSMS
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendSMS $event): void
    {
        $phoneNumber = $event->phoneNumber;
        $message     = $event->message;
        $phoneNumber = "88" . $phoneNumber;

        if ($phoneNumber && $message) {
            $sms = new SMS();
            $sms->sendSMS($phoneNumber, $message);
        }
    }
}
