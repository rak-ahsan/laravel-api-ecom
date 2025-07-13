<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "name"        => $this->name,
            "slug"        => $this->slug,
            "category"    => CategoryResource::make($this->whenLoaded("category")),
            "status"      => $this->status,
            "image"       => Helper::getFilePath($this->img_path),
            "created_by"  => $this->created_by,
            "updated_by"  => $this->updated_by
        ];
    }
}
