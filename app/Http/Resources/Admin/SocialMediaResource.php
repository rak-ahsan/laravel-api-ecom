<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"     => $this->id,
            "title"  => $this->title,
            "link"   => $this->link,
            "status" => $this->status,
            "image"  => Helper::getFilePath($this->img_path)
        ];
    }
}
