<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name"        => ["required", "string", "max:50"],
            "designation" => ["required", "string", "max:50"],
            "description" => ["required", "string", "max:500"],
            "image"       => ["sometimes", "nullable", "image", "mimes:jpeg,png,jpg,gif,webp,svg"],
            'status'      => ['required', new EnumValidation(StatusEnum::class)]
        ];
    }
}
