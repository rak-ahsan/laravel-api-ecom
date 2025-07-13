<?php

namespace App\Http\Resources\Front;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"               => $this->id,
            "username"         => $this->username,
            "email"            => $this->email,
            "phone_number"     => $this->phone_number,
            "verification_otp" => $this->verification_otp,
            "is_verified"      => $this->is_verified,
            "img_path"         => Helper::getFilePath($this->img_path),
            "points"           => $this->points,
        ];
    }
}
