<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'  => ['nullable', "string"],
            'type'   => ['required', 'string'],
            'image'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg'],
            'status' => ['required', new EnumValidation(StatusEnum::class)],
        ];
    }
}
