<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MacAddress extends BaseModel
{
    public function macAddressDetails(): HasMany
    {
        return $this->hasMany(MacAddressDetails::class, "mac_address_id", "id");

        // return $this->hasMany(MacAddressDetails::class)->where('status', 'active');
    }

}
