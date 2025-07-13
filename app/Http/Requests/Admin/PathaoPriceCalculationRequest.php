<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PathaoPriceCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "store_id"       => ["required"],
            "item_type"      => ["required"],
            "delivery_type"  => ["required"],
            "item_weight"    => ["required"],
            "recipient_city" => ["required"],
            "recipient_zone" => ["required"]
        ];
    }
}
