<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryGatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "delivery_fee" => $this->delivery_fee,
            "min_time"     => $this->min_time,
            "max_time"     => $this->max_time,
            "time_unit"    => $this->time_unit,
            "status"       => $this->status,
        ];
    }
}
