<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "user_id"    => $this->user_id,
            "product_id" => $this->product_id,
            "rate"       => $this->rate,
            "status"     => $this->status,
            "comment"    => $this->comment,
            "crated_by"  => $this->cratedBy
        ];
    }
}
