<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RedxParcelCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "customer_name"                  => ["required", "string"],
            "customer_phone"                 => ["required", "string"],
            "delivery_area"                  => ["required", "string"],
            "delivery_area_id"               => ["required"],
            "customer_address"               => ["required", "string"],
            "merchant_invoice_id"            => ["nullable", "string"],
            "cash_collection_amount"         => ["required"],
            "parcel_weight"                  => ["required"],
            "instruction"                    => ["nullable", "string"],
            "value"                          => ["required"],
            "parcel_details_json"            => ["nullable", "array"],
            "parcel_details_json.*.name"     => ["nullable", "string", "required_with:parcel_details_json"],
            "parcel_details_json.*.category" => ["nullable", "string", "required_with:parcel_details_json"],
            "parcel_details_json.*.value"    => ["nullable", "required_with:parcel_details_json"]
        ];
    }
}
