<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends BaseModel
{
    public function attributeValues() : HasMany
    {
        return $this->hasMany(AttributeValue::class, "attribute_id", "id");
    }
}
