<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "purchase_code" => $this->purchase_code,
            "quantity"      => $this->quantity,
            "cost"          => $this->cost,
            "buy_price"     => $this->buy_price,
            "paid_amount"   => $this->paid_amount,
            "due_amount"    => $this->due_amount,
            "paid_status"   => $this->paid_status,
            "details"       => PurchaseDetailResource::collection($this->whenLoaded("purchaseDetails")),
            "supplier"      => SupplierResource::make($this->whenLoaded("supplier")),
            "created_by"    => $this->whenLoaded('createdBy'),
            "updated_by"    => $this->whenLoaded('updatedBy')
        ];
    }
}
