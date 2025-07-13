<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            "title"       => ["required", "unique:blog_posts,title,$id"],
            "category_id" => ["nullable", "string", Rule::exists('categories', 'id')],
            "image"       => ["sometimes", "nullable", "mimes:jpeg,png,jpg,gif,webp,svg"],
            "description" => ["required", "string"],
            'status'      => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
