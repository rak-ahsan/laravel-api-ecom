<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "name"        => $this->name,
            "designation" => $this->designation,
            "status"      => $this->status,
            "image"       => Helper::getFilePath($this->img_path),
            "description" => $this->description,
            "created_at"  => $this->created_at,
            "created_by"  => $this->whenLoaded('createdBy')
        ];
    }
}
