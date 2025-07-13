<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'image'       => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg']
        ];
    }
}
