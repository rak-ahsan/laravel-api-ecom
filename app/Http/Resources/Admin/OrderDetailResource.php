<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "quantity"   => $this->quantity,
            "buy_price"  => $this->buy_price,
            "mrp"        => $this->mrp,
            "sell_price" => $this->sell_price,
            "discount"   => $this->discount,
        ];
    }
}
