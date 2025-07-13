<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PathaoEnvCredentialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            "endpoint"      => $this->endpoint,
            "client_id"     => $this->client_id,
            "client_secret" => $this->client_secret,
            "username"      => $this->username,
            "password"      => $this->password,
            "grant_type"    => $this->grant_type,
            "created_by"    => $this->whenLoaded("createdBy"),
            "updated_by"    => $this->whenLoaded("updatedBy"),
        ];
    }
}
