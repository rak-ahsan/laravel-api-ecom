<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PathaoCreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "sender_name"       => ["required", "string", "min:3", "max:64"],
            "sender_phone"      => ["required", "string"],
            "recipient_name"    => ["required", "string"],
            "recipient_phone"   => ["required", "string"],
            "recipient_address" => ["required", "string", "min:10", "max:65"],
            "store_id"          => ["required", "integer"],
            "recipient_city_id" => ["required", "integer"],
            "recipient_zone_id" => ["required", "integer"],
            "delivery_type"     => ["required", "integer"],
            "item_type"         => ["required", "integer"],
            "item_weight"       => ["required", "numeric", "between:0.5,10.0"],
            "collect_amount"    => ["required"]
        ];
    }
}
