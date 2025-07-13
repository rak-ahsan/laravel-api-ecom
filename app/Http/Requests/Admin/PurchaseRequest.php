<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "supplier_id"                  => ["required", "integer", Rule::exists("suppliers", "id")],
            "cost"                         => ["required", "min:0"],
            "paid_amount"                  => ["required", "min:0"],
            "paid_status"                  => ["required"],
            "items"                        => ["required", "array"],
            "items.*.product_id"           => ["required"],
            "items.*.attribute_value_id_1" => ["nullable"],
            "items.*.attribute_value_id_2" => ["nullable"],
            "items.*.attribute_value_id_3" => ["nullable"],
            "items.*.buy_price"            => ["required"],
            "items.*.quantity"             => ["required"],
        ];
    }
}
