<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "current_stock"     => $this->current_stock,
            "is_default"        => $this->is_default,
            "buy_price"         => $this->buy_price,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "sell_price"        => $this->sell_price,
            "offer_percent"     => $this->offer_percent,
            "image"             => Helper::getFilePath($this->img_path),
            "attribute_value_1" => $this->whenLoaded("attributeValue1"),
            "attribute_value_2" => $this->whenLoaded("attributeValue2"),
            "attribute_value_3" => $this->whenLoaded("attributeValue3"),
        ];
    }
}
