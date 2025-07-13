<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SteadFastEnvCredentialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "endpoint"   => $this->endpoint,
            "api_key"    => $this->api_key,
            "secret_key" => $this->secret_key,
            "created_by" => $this->whenLoaded("createdBy"),
            "updated_by" => $this->whenLoaded("updatedBy"),
        ];
    }
}
