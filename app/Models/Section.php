<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends BaseModel
{
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, "section_products", "section_id", "product_id");
    }
}
