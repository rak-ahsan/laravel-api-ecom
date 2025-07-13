<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class DistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'   => ['required', "unique:districts,name,$id"],
            'status' => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
