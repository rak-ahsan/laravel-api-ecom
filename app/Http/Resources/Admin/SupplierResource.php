<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "phone_number" => $this->phone_number,
            "email"        => $this->email,
            "status"       => $this->status,
            "address"      => $this->address,
            "created_by"   => $this->created_by,
            "updated_by"   => $this->updated_by
        ];
    }
}
