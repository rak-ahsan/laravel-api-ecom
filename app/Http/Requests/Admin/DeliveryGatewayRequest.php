<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'   => ['required', "unique:delivery_gateways,name,$id"],
            'status' => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
