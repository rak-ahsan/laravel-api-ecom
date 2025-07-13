<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MacAddressDetails extends BaseModel
{
    public function macAddress(): BelongsTo
    {
        return $this->belongsTo(MacAddress::class, "mac_address_id", "id");

    }

}
