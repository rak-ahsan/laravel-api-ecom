<?php

namespace App\Http\Requests\Front;

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
            "product_id" => ["required", "integer"],
            'rate'       => ['required', 'numeric', "min:1", "max:5"],
            'comment'    => ['nullable', "string"]
        ];
    }
}
