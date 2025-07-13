<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RawMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'name'      => ['required', "unique:raw_materials,name,$id"],
            "unit_cost" => ["required", "numeric"],
            "quantity"  => ["required", "integer"]
        ];
    }
}
