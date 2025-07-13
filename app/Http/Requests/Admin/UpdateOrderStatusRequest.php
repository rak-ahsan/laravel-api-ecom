<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "order_ids"         => ["required", "array"],
            "current_status_id" => ["required", "integer", Rule::exists("suppliers", "id")]
        ];
    }
}
