<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'        => ['required', "unique:sub_categories,name,$id"],
            'image'       => ['nullable'],
            "category_id" => ["required", "string", Rule::exists('categories', 'id')],
        ];
    }
}
