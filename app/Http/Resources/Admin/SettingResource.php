<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "key"        => $this->key,
            "value"      => $this->value,
            "image"      => Helper::getFilePath($this->img_path),
            "status"     => $this->status,
            "created_at" => $this->created_at,
            "crated_by"  => $this->cratedBy
        ];
    }
}


