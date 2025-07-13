<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use App\Enums\OrderGuardTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class OrderGuardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration_type' => ['nullable', new EnumValidation(OrderGuardTypeEnum::class)],
            'quantity'      => ['required',],
            'duration'      => ['required',],
            'status'        => ['required', new EnumValidation(StatusEnum::class)]
        ];
    }
}
