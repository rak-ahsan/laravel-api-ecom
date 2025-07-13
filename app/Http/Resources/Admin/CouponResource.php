<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"              => $this->id,
            "name"            => $this->name,
            "discount_type"   => $this->discount_type,
            "discount_amount" => $this->discount_amount,
            "min_cart_amount" => $this->min_cart_amount,
            "started_at"      => $this->started_at,
            "ended_at"        => $this->ended_at,
            "code"            => $this->code,
            "status"          => $this->status
        ];
    }
}
