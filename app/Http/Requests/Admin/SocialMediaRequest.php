<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class SocialMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "link"   => ["required"],
            "status" => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
