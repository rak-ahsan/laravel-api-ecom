<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdditionalCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "start_date" => ["required", "date"],
            "end_date"   => ["nullable", "date"],
            "cost"       => ["required", "numeric"],
        ];
    }
}
