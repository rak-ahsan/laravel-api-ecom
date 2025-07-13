<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "purchase_id"      => $this->purchase_id,
            "buy_price"        => $this->buy_price,
            "quantity"         => $this->quantity,
            "total"            => $this->total,
            "product"          => $this->whenLoaded("product"),
            "attribute_value1" => $this->whenLoaded("attributeValue1"),
            "attribute_value2" => $this->whenLoaded("attributeValue2"),
            "attribute_value3" => $this->whenLoaded("attributeValue3")
        ];
    }
}
