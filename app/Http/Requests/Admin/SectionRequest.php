<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title"       => ["required"],
            "product_ids" => ["required", "array"],
            'status'      => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
