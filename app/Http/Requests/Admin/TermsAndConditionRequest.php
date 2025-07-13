<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class TermsAndConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'title'       => ['required', "unique:terms_and_conditions,title,$id"],
            'description' => ['required', "string"],
            'status'      => ['required', new EnumValidation(StatusEnum::class)]
        ];
    }
}
