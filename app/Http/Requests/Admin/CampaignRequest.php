<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title"                 => ["required", "string"],
            "start_date"            => ["required"],
            "end_date"              => ["required"],
            "items"                 => ["required", "array"],
            "items.*.product_id"    => ["required"],
            "items.*.discount"      => ["required"],
            "items.*.discount_type" => ["required", "in:fixed,percentage"],
            'status'                => ["required", new EnumValidation(StatusEnum::class)]
        ];
    }
}
