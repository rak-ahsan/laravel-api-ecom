<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
           'name'  => ['required', "unique:categories,name,{$id}"],
           'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg'],
        ];
    }
}
