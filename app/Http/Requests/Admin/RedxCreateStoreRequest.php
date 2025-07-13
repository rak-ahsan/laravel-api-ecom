<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RedxCreateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name"    => ["required", "string"],
            "phone"   => ["required", "string"],
            "address" => ["required", "string"],
            'area_id' => ["required"]
        ];
    }
}
