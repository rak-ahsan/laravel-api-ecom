<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "value"      => $this->value,
            "created_at" => $this->created_at,
            "created_by" => $this->whenLoaded("createdBy"),
            "attribute"  => $this->whenLoaded("attribute")
        ];
    }
}
