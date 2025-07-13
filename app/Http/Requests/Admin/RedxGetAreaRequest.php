<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RedxGetAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "zone_id"       => ["required", "integer"],
            "postal_code"   => ["required"],
            "district_name" => ["required"]
        ];
    }
}
