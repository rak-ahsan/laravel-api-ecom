<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreeDeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "type"       => $this->type,
            "quantity"   => $this->quantity,
            "price"      => $this->price,
            "status"     => $this->status,
            "created_at" => $this->created_at,
            "created_by" => $this->whenLoaded('createdBy')
        ];
    }
}
