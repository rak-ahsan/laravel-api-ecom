<?php

namespace App\Repositories;

use App\Models\SteadFast;
use Exception;
use App\Models\Order;
use App\Classes\Helper;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Http;

class SteadFastRepository
{
    private $endPoint;
    private $apiKey;
    private $secretKey;

    public function __construct()
    {
        $this->endPoint  = config("stead_fast.endpoint");
        $this->apiKey    = config("stead_fast.api_key");
        $this->secretKey = config("stead_fast.secret_key");
    }

    public function createOrder($request) {

        $orderId = $request->input('order_id', null);

        $order = Order::find($orderId);
        if (!$order) {
            throw new CustomException("Invalid order");
        }

        $url = "{$this->endPoint}/create_order";

        $headers = $this->getHeaders();

        $body = [
            'invoice'           => $orderId,
            'recipient_name'    => $request->customer_name,
            'recipient_phone'   => Helper::formatPhoneNumber($request->phone_number),
            'recipient_address' => $request->address_details,
            'cod_amount'        => round($request->payable_price),
            'note'              => $request->note
        ];

        $res = Http::withHeaders($headers)->post($url, $body);

        $jsonRes = json_decode($res, true);

        // Check error
        if ($jsonRes["status"] === 400) {
            $error = 'Error from stead fast';
            if (isset($result["errors"]) && array_key_exists("invoice", $jsonRes["errors"])) {
                $error = $jsonRes["errors"]["invoice"];
            }

            throw new CustomException($error);
        }

        // Update order information
        if ($jsonRes["status"] === 200) {
            $order->consignment_id = $jsonRes["consignment"]["consignment_id"];
            $order->tracking_code  = $jsonRes["consignment"]["tracking_code"];
            $order->courier_name   = 'SteadFast';
            $order->save();
        }

        return $jsonRes;
    }

    public function getDeliveryStatus($invoiceId)
    {
        try {
            $url = "{$this->endPoint}/status_by_invoice/{$invoiceId}";

            $headers = $this->getHeaders();

            $res = Http::withHeaders($headers)->get($url);

            $jsonRes = json_decode($res, true);

            return $jsonRes;

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getCurrentBalance()
    {
        try {
            $url = "{$this->endPoint}/get_balance";

            $headers = $this->getHeaders();

            $res = Http::withHeaders($headers)->get($url);

            $jsonRes = json_decode($res, true);

            return $jsonRes;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    private function getHeaders()
    {
        return [
            "Api-Key"      => $this->apiKey,
            "Secret-Key"   => $this->secretKey,
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];
    }

    public function updateEnvCredential($request)
    {
        try {
            $data = [
                'STEAD_FAST_ENDPOINT'   => $request->stead_fast_endpoint,
                'STEAD_FAST_API_KEY'    => $request->stead_fast_api_key,
                'STEAD_FAST_SECRET_KEY' => $request->stead_fast_secret_key
            ];

            Helper::updateEnvVariable($data);

            $steadFast = SteadFast::firstOrNew();

            $steadFast->endpoint   = $request->stead_fast_endpoint;
            $steadFast->api_key    = $request->stead_fast_api_key;
            $steadFast->secret_key = $request->stead_fast_secret_key;
            $steadFast->save();

            return $steadFast;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function show()
    {
        try {
            return SteadFast::with(["createdBy:id,username", "updatedBy:id,username"])->first();
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
