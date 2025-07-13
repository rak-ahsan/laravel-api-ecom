<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"             => $this->id,
            "phone_number_1" => $this->phone_number_1,
            "phone_number_2" => $this->phone_number_2,
            "email"          => $this->email,
            "address"        => $this->address
        ];
    }
}
