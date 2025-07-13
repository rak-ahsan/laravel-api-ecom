<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required'],
            'rate'       => ['required', 'numeric', "min:1", "max:5"],
            'comment'    => ['nullable', "string"],
            'status'     => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
