<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "display_name" => $this->display_name,
            "name"         => $this->name,
            "description"  => $this->description,
            "created_at"   => $this->created_at,
            "permissions"  => PermissionCollection::make($this->whenLoaded('permissions')),
            "created_by"   => $this->whenLoaded('createdBy')
        ];
    }
}
