<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "username"     => $this->username,
            "phone_number" => $this->phone_number,
            "email"        => $this->email,
            "status"       => $this->status,
            "is_active"    => $this->is_active,
            "image"        => Helper::getFilePath($this->img_path),
            "category"     => $this->whenLoaded("userCategory"),
            "roles"        => RoleCollection::make($this->whenLoaded("roles")),
        ];
    }
}
