<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "start_date" => $this->start_date,
            "end_date"   => $this->end_date,
            "status"     => $this->status,
            "products"   => $this->whenLoaded("campaignProducts", function() {
                return CampaignProductResource::collection($this->campaignProducts);
            })
        ];
    }
}
