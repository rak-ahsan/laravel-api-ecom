<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "slug"       => $this->slug,
            "status"     => $this->status,
            "bg_color"   => $this->bg_color,
            "text_color" => $this->text_color,
            "created_at" => $this->created_at,
            "created_by" => $this->whenLoaded('createdBy')
        ];
    }
}

