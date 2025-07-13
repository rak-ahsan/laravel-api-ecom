<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyPolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "description" => $this->description,
            "status"      => $this->status,
            "created_by"  => $this->whenLoaded('createdBy'),
            "updated_by"  => $this->whenLoaded('updated_by'),
            "deleted_by"  => $this->whenLoaded('deleted_by')
        ];
    }
}
