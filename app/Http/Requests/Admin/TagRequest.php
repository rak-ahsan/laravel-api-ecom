<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"   => ["required", "string", "unique:tags,name,$id"],
            'status' => ['required', new EnumValidation(StatusEnum::class)],
        ];
    }
}
