<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            "title"  => ["required", "string","unique:sliders,title,$id"],
            'status' => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
