<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductVariationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "is_default"    => $this->is_default,
            "mrp"           => $this->mrp,
            "offer_price"   => $this->offer_price,
            "discount"      => $this->discount,
            "offer_percent" => $this->offer_percent,
            "image"         => Helper::getFilePath($this->img_path),
        ];
    }
}
