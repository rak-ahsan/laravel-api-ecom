<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "key"                 => ["required", "string"],
            "value"               => ["required", "string"],
            "setting_category_id" => ["nullable", Rule::exists("setting_categories", "id")],
            'status'              => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
