<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use App\Enums\FreeDeliveryTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class FreeDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'     => ['nullable', new EnumValidation(FreeDeliveryTypeEnum::class)],
            'quantity' => ['required', ],
            'price'    => ['required', ],
            'status'   => ['required', new EnumValidation(StatusEnum::class)]
        ];
    }
}
