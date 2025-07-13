<?php

namespace App\Http\Controllers\Front;

use Exception;
use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CouponController extends BaseController
{
    public function index()
    {
        try {
            $coupon = Coupon::select(
                'id', 'name', 'code', 'discount_type', 'discount_amount', 'min_cart_amount', 'description',
                'started_at', 'ended_at'
            )
            ->where('status', 'active')->first();

            return $this->sendResponse($coupon, 'Coupon list', 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function checkCouponCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code'       => ["required", "string", "max:20"],
            'cart_total_amount' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $couponCode      = $request->input('coupon_code', null);
        $couponCode      = Str::of($couponCode)->trim();
        $cartTotalAmount = $request->input('cart_total_amount', 0);

        if (!$couponCode) {
            return $this->sendError('Coupon code not found');
        }

        $now    = Carbon::now();
        $coupon = Coupon::where('code', $couponCode)
            ->where('status', 'active')
            ->whereDate('started_at', '<=', $now)
            ->whereDate('ended_at', '>=', $now)->first();

        if (!$coupon) {
            return $this->sendError('Invalid coupon code');
        }

        $minCartValue = $coupon->min_cart_amount;
        if ($cartTotalAmount < $minCartValue) {
            $msg = "Minimum cart amount without delivery charge {$minCartValue} is required";
            return $this->sendError($msg);
        }

        // Calculate discount amount
        $discountAmount = 0;
        if ($coupon->discount_type === "fixed") {
            $discountAmount = $cartTotalAmount - $coupon->discount_amount;
        } else {
            $discountAmount = ($cartTotalAmount * $coupon->discount_amount) / 100;
        }

        $data = [
            "discount_amount" => $discountAmount,
            "coupon_id" => $coupon->id,
        ];

        return $this->sendResponse($data, 'Coupon discount amount', 200);
    }
}
