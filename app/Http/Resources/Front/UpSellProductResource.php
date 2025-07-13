<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpSellProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "offer_price"   => $this->offer_price,
            "discount"      => $this->discount,
            "offer_percent" => $this->offer_percent,
            "sell_price"    => $this->sell_price,
            "current_stock" => $this->current_stock,
            "free_shipping" => $this->free_shipping,
            "image"         => Helper::getFilePath($this->img_path),
            "variations"    => ProductVariationResource::collection($this->whenLoaded("variations")),
        ];
    }
}
