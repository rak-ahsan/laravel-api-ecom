<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Resources\Front\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variations = $this->whenLoaded('variations');

        // get minimum and maximum variation prices
        $variationMinPrice = $variations->min('sell_price');
        $variationMaxPrice = $variations->max('sell_price');

        // get minimum and maximum Current Stock variation Product
        $minCurrentStock = $variations->min('current_stock');
        $maxCurrentStock = $variations->max('current_stock');

        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "slug"              => $this->slug,
            "status"            => $this->status,
            "buy_price"         => $this->buy_price,
            "mrp"               => $this->mrp,
            "offer_price"       => $this->offer_price,
            "discount"          => $this->discount,
            "offer_percent"     => $this->offer_percent,
            "current_stock"     => $this->current_stock,
            "minimum_qty"       => $this->minimum_qty,
            "alert_qty"         => $this->alert_qty,
            "type"              => $this->type,
            "sku"               => $this->sku,
            "free_shipping"     => $this->free_shipping,
            "description"       => $this->description,
            "short_description" => $this->short_description,
            "image"             => Helper::getFilePath($this->img_path),
            "video_url"         => $this->video_url,
            "meta_title"        => $this->meta_title,
            "meta_keywords"     => $this->meta_keywords,
            "meta_description"  => $this->meta_description,
            "brand"             => $this->whenLoaded("brand"),
            "category"          => $this->whenLoaded("category"),
            "sub_category"      => $this->whenLoaded("subCategory"),
            "images"            => ImageResource::collection($this->whenLoaded('images')),
            "variations"        => ProductVariationResource::collection($variations),
            'variation_price_range'   => [
                "min_price" => $variationMinPrice,
                "max_price" => $variationMaxPrice,
            ],
            'current_stock_range'   => [
                "min_current_stock" => $minCurrentStock,
                "max_current_stock" => $maxCurrentStock,
            ],
            "up_sell_products" => ProductCollection::make($this->whenLoaded("upSellProducts")),
        ];
    }
}
