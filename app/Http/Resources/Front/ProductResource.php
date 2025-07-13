<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variations = $this->whenLoaded('variations');

        // Group attributes by name
        $groupedAttributes = $this->groupAttributes($variations);

        // get minimum and maximum variation prices
        $variationMinPrice = $variations->min('sell_price');
        $variationMaxPrice = $variations->max('sell_price');

        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "slug"              => $this->slug,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "sell_price"        => $this->sell_price,
            "current_stock"     => $this->current_stock,
            "minimum_qty"       => $this->minimum_qty,
            "alert_qty"         => $this->alert_qty,
            "type"              => $this->type,
            "free_shipping"     => $this->free_shipping,
            "image"             => Helper::getFilePath($this->img_path),
            "description"       => $this->description,
            "short_description" => $this->short_description,
            "video_url"         => $this->video_url,
            "brand"             => $this->brand ?? null,
            "category"          => $this->category ?? null,
            "sub_category"      => $this->subCategory ?? null,
            'variation_price_range'   => [
                "min_price" => $variationMinPrice,
                "max_price" => $variationMaxPrice,
            ],
            "images"            => ImageResource::collection($this->whenLoaded("images")),
            "variations"        => [
                "data"       => ProductVariationResource::collection($this->whenLoaded("variations")),
                "attributes" => $groupedAttributes,
            ],
            "up_sell_products"  => UpSellProductResource::collection($this->whenLoaded("upSellProducts"))
        ];
    }

    private function groupAttributes($variations)
    {
        $attributes = [];

        foreach ($variations as $variation) {
            // Loop through possible attribute values
            for ($i = 1; $i <= 3; $i++) {
                $attributeValue = $variation->{"attributeValue$i"} ?? null;
                if ($attributeValue) {
                    $attributeName = @$attributeValue->attribute->name;

                    // Check if the attribute name exists in the array
                    if (!isset($attributes[$attributeName])) {
                        $attributes[$attributeName] = [];
                    }

                    // Add unique attributes to the array
                    $attributes[$attributeName][$attributeValue->id] = [
                        'attribute_value_id' => $attributeValue->id,
                        'attribute_value'    => $attributeValue->value,
                        'attribute_id'       => $attributeValue->attribute_id
                    ];
                }
            }
        }

        // Flatten the arrays to ensure that each attribute type is properly formatted
        foreach ($attributes as &$attributeGroup) {
            $attributeGroup = array_values($attributeGroup);
        }

        return $attributes;
    }
}
