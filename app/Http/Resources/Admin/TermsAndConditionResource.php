<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TermsAndConditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "description" => $this->description,
            "status"      => $this->status,
            "created_at"  => $this->created_at,
            "created_by"  => $this->whenLoaded('createdBy')
        ];
    }
}
