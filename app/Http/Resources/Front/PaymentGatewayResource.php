<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $filePath = Helper::getFilePath($this->img_path);

        return [
            "id"     => $this->id,
            "name"   => $this->name,
            "slug"   => $this->slug,
            "status" => $this->status,
            "image"  => $filePath
        ];
    }
}
