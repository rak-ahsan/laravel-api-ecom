<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends BaseModel
{
    public function attribute() : BelongsTo
    {
        return $this->belongsTo(Attribute::class, "attribute_id", "id");
    }
}
