<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "coupon_value"     => $this->coupon_value,
            "delivery_charge"  => $this->delivery_charge,
            "buy_price"        => $this->buy_price,
            "mrp"              => $this->mrp,
            "sell_price"       => $this->sell_price,
            "discount"         => $this->discount,
            "special_discount" => $this->special_discount,
            "net_order_price"  => $this->net_order_price,
            "advance_payment"  => $this->advance_payment,
            "payable_price"    => $this->payable_price,
            "courier_payable"  => $this->courier_payable,
            "paid_status"      => $this->paid_status,
            "phone_number"     => $this->phone_number,
            "customer_name"    => $this->customer_name,
            "district"         => $this->district,
            "address_details"  => $this->address_details,
            "order_from"       => $this->order_from,
            "consignment_id"   => $this->consignment_id,
            "tracking_code"    => $this->tracking_code,
            "courier_name"     => $this->courier_name,
            "note"             => $this->note,
            "created_at"       => $this->created_at,
            "prepared_at"      => $this->prepared_at,
            "prepared_by"      => $this->whenLoaded('preparedBy'),
            "created_by"       => $this->whenLoaded('createdBy'),
            "updated_by"       => $this->whenLoaded('updatedBy'),
            "delivery_gateway" => $this->whenLoaded('deliveryGateway'),
            "payment_gateway"  => $this->whenLoaded('paymentGateway'),
            "coupon"           => $this->whenLoaded('coupon'),
            "current_status"   => StatusResource::make($this->whenLoaded('currentStatus')),
            "statuses"         => StatusCollection::make($this->whenLoaded('statuses')),
            "details"          => $this->whenLoaded('details'),
            "raw_materials"    => $this->whenLoaded('rawMaterials')
        ];
    }
}
