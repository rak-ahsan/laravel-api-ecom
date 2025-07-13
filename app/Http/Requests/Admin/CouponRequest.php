<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            "name"            => ["required", "unique:coupons,name,$id"],
            "code"            => ["required", "unique:coupons,code,$id"],
            "discount_amount" => ["required"],
            "started_at"      => ["required"],
            "ended_at"        => ["required"],
            'status'          => ["required", new EnumValidation(StatusEnum::class)]

        ];
    }
}
