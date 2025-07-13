<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddRawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "order_id"                        => ["required", Rule::exists("orders", 'id')],
            "raw_materials"                   => ["required", "array"],
            "raw_materials.*.raw_material_id" => ["required", "integer"],
            "raw_materials.*.quantity"        => ["required", "integer"]
        ];
    }
}
