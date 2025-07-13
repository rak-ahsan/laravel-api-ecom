<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SteadFastCreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'        => ['required', 'integer'],
            'customer_name'   => ['required', 'string'],
            'phone_number'    => ['required', 'string'],
            'address_details' => ['required', 'string'],
            'payable_price'   => ['required', 'string'],
        ];
    }
}
