<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PathaoUpdateEnvCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "pathao_endpoint"      => ["required"],
            "pathao_client_id"     => ["required"],
            "pathao_client_secret" => ["required"],
            "pathao_username"      => ["required"],
            "pathao_password"      => ["required"],
            "pathao_grant_type"    => ["required"],
        ];
    }
}
