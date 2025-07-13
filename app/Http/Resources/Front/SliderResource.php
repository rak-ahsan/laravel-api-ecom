<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"     => $this->id,
            "title"  => $this->title,
            "status" => $this->status,
            "image"  => Helper::getFilePath($this->img_path)
        ];
    }
}
