<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_gateway_id'           => ['nullable', Rule::exists('payment_gateways', 'id')],
            'delivery_gateway_id'          => ['nullable', Rule::exists('delivery_gateways', 'id')],
            'current_status_id'            => ['nullable', Rule::exists('statuses', 'id')],
            'coupon_id        '            => ['nullable', 'integer', Rule::exists('coupons', 'id')],
            'customer_name'                => ['required', 'string', 'max:100'],
            'phone_number'                 => ['required', "min:11", 'numeric'],
            'address_details'              => ['required', 'string', 'max:250'],
            'delivery_charge'              => ['required', 'min:0'],
            'items'                        => ['required'],
            'items.*.product_id'           => ['required', 'integer'],
            'items.*.attribute_value_id_1' => ['nullable'],
            'items.*.attribute_value_id_2' => ['nullable'],
            'items.*.attribute_value_id_3' => ['nullable'],
            'items.*.quantity'             => ['required', 'integer']
        ];
    }
}
