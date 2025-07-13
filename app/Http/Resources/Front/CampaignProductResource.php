<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"             => $this->id,
            "discount_type"  => $this->discount_type,
            "mrp"            => $this->mrp,
            "offer_price"    => $this->offer_price,
            "discount"       => $this->discount,
            "discount_value" => $this->discount_value,
            "product"        => $this->whenLoaded("product", function() {
                return [
                    "id"    => $this->id,
                    "name"  => $this->name,
                    "image" => Helper::getFilePath($this->img_path),
                ];
            }),
            "variations"     => $this->whenLoaded("campaignProductVariations", function () {
                return CampaignProductVariationResource::collection($this->campaignProductVariations);
            })
        ];
    }
}
