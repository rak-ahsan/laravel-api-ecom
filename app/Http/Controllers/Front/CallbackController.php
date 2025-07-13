<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Classes\BaseController;

class CallbackController extends BaseController
{
    public function callback(Request $request)
    {
        $status         = $request->input("status");
        $tranId         = $request->input("tran_id");
        $error          = $request->input("error");
        $bankTranId     = $request->input("bank_tran_id");
        $currency       = $request->input("currency");
        $tranDate       = $request->input("tran_date");
        $amount         = $request->input("amount");
        $storeId        = $request->input("store_id");
        $currencyType   = $request->input("currency_type");
        $currencyAmount = $request->input("currency_amount");
        $currencyRate   = $request->input("currency_rate");
        $baseFair       = $request->input("base_fair");
        $valueA         = $request->input("value_a");
        $valueB         = $request->input("value_b");
        $valueC         = $request->input("value_c");
        $valueD         = $request->input("value_d");
        $verifySign     = $request->input("verify_sign");
        $verifySignSha2 = $request->input("verify_sign_sha2");
        $verifyKey      = $request->input("verify_key");

        $type = null;
        $order = Order::find($tranId);
        if ($status === 'VALID') {
            $order->is_paid = true;
            $order->save();
            $type = 'success';
        } else if ($status === 'FAILED') {
            $type = 'failed';
        } else {
            $type = 'cancel';
        }

        return $this->returnView($type);
    }

    private function httpGet($url)
    {
        $ch      = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }

    private function returnView($type)
    {
        $frontBaseURL = config('app.front_url');
        if ($type === 'success') {
            return redirect("{$frontBaseURL}/your-payment-success");
        } else if ($type === 'fail') {
            return redirect("{$frontBaseURL}/your-payment-failed");
        } else if ($type === 'cancel') {
            return redirect("{$frontBaseURL}/your-payment-cancelled");
        } else if ($type === 'ipn') {
            return '';
        } else {
            return redirect("{$frontBaseURL}/your-payment-failed");
        }
    }
}
