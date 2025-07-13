<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderGuardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "quantity"      => $this->quantity,
            "duration"      => $this->duration,
            "duration_type" => $this->duration_type,
            "status"        => $this->status,
            "created_at"    => $this->created_at,
            "created_by"    => $this->whenLoaded('createdBy')
        ];
    }
}
