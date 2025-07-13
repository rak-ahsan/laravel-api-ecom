<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RedxEnvCredentialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "endpoint"   => $this->endpoint,
            "token"      => $this->token,
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy"),
        ];
    }
}
