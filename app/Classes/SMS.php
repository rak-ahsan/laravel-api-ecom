<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class SMS
{
    private $endPoint;
    private $apiKey;
    private $senderId;

    public function __construct()
    {
        $this->endPoint = config("sms.endpoint");
        $this->apiKey   = config("sms.api_key");
        $this->senderId = config("sms.sender_id");
    }

    public function sendSMS($phoneNumber, $message)
    {
        $header= [
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];

        $body = [
            "api_key"  => $this->apiKey,
            "type"     => "text",
            "contacts" => $phoneNumber,
            "senderid" => $this->senderId,
            "msg"      => $message,
        ];

        $res = Http::withHeaders($header)->post($this->endPoint, $body);

        return json_decode($res, true);
    }

    public function getBalance()
    {
        $res = Http::get($this->endPoint);

        return json_decode($res, true);
    }
}
