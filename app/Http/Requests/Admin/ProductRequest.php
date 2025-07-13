<?php

namespace App\Http\Requests\Admin;

use App\Classes\Helper;
use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        $rules = [
            "name"                              => ["required", "unique:products,name,$id"],
            "brand_id"                          => ["nullable", Rule::exists('brands', 'id')],
            "category_id"                       => ["required", "integer",  Rule::exists('categories', 'id')],
            "status"                            => ["required", new EnumValidation(StatusEnum::class)],
            "buy_price"                         => ["nullable", "numeric", "min:0"],
            "mrp"                               => ["nullable", "numeric", "min:0"],
            "free_shipping"                     => ["boolean"],
            "up_sell_product_ids"               => ["nullable", "array"],
            "gallery_images"                    => ["nullable", "array"],
            "gallery_images.*"                  => ["image", "mimes:jpeg,jpg,png,gif,webp", "max:2048"],
            "variations"                        => ["required_if:buy_price,0","required_if:mrp,0","array"],
            "variations.*.attribute_value_id_1" => ["nullable", "integer"],
            "variations.*.attribute_value_id_2" => ["nullable", "integer"],
            "variations.*.attribute_value_id_3" => ["nullable", "integer"],
            "variations.*.buy_price"            => ["required_with:variations", "numeric", "min:0"],
            "variations.*.mrp"                  => ["required_with:variations", "numeric", "min:0"],
            "variations.*.offer_price"          => ["required_with:variations", "numeric", "min:0"],
        ];

        if (!$id) {
            $rules["image"] = ["required", "image", "mimes:jpeg,jpg,png,gif,webp", "max:2048"];
        } else {
            $rules["image"] = ["nullable", "image", "mimes:jpeg,jpg,png,gif,webp", "max:2048"];
        }

        if (Helper::getSettingValue("is_stock_maintain_with_direct_product")) {
            $rules["current_stock"] = ['required', 'numeric', 'min:1'];
        } else {
            $rules["current_stock"] = ['nullable'];
        }

        return $rules;
    }
}
