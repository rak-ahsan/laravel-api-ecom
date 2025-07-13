<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SteadFastUpdateEnvCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "stead_fast_endpoint"   => ["required"],
            "stead_fast_api_key"    => ["required"],
            "stead_fast_secret_key" => ["required"]
        ];
    }
}
