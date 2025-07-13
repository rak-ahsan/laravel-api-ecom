<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"        => $this->id,
            "title"     => $this->title,
            "products"  => ProductCollection::make($this->whenLoaded("products")),
            "crated_by" => $this->whenLoaded("cratedBy"),
            "updatedBy" => $this->whenLoaded("updatedBy")
        ];
    }
}
