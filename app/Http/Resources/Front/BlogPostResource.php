<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"          => $this->id,
            "title"       => $this->title,
            "status"      => $this->status,
            "description" => $this->description,
            "image"       => Helper::getFilePath($this->img_path),
            "tags"        => $this->whenLoaded('tags')
        ];
    }
}
