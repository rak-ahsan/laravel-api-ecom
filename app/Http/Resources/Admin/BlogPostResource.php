<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "title"            => $this->title,
            "status"           => $this->status,
            "image"            => Helper::getFilePath($this->img_path),
            "description"      => $this->description,
            "meta_title"       => $this->meta_title,
            "meta_tag"         => $this->meta_tag,
            "meta_description" => $this->meta_description,
            "created_at"       => $this->created_at,
            "created_by"       => $this->whenLoaded('createdBy'),
            "category"         => $this->whenLoaded('category'),
            "tags"             => $this->whenLoaded('tags')
        ];
    }
}
