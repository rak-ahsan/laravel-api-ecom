<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends BaseModel
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'current_status_id', 'id');
    }
}
