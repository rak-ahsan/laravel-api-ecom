<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $filePath = Helper::getFilePath("uploads/products/variationImage", $this->image);

        return [
            "id"                => $this->id,
            "title"             => $this->title,
            "start_date"        => $this->start_date,
            "end_date"          => $this->end_date,
            "status"            => $this->status,
            "created_by"        => $this->whenLoaded('createdBy'),
            "updated_by"        => $this->whenLoaded('updated_by'),
            "deleted_by"        => $this->whenLoaded('deleted_by'),
            "campaign_products" => $this->whenLoaded('campaignProducts')
        ];
    }
}
