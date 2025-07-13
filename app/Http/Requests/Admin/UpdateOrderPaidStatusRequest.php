<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPaidStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "paid_status" => ["required", "in:paid,unpaid"],
            "order_ids"   => ["required", "array"]
        ];
    }
}
