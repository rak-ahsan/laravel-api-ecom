<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends BaseModel
{
    public $uploadPath = 'uploads/subCategories';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
