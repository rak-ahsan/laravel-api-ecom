<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PathaoCreateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name"              => ["required", "string"],
            "contact_name"      => ["required", "string"],
            "contact_number"    => ["required", "string"],
            "secondary_contact" => ["nullable", "string"],
            "address"           => ["required", "min:10", "max:65"],
            "city_id"           => ["required", "integer"],
            "zone_id"           => ["required", "integer"],
            "area_id"           => ["required", "integer"]
        ];
    }
}
