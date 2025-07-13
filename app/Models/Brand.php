<?php

namespace App\Models;

use App\Classes\BaseModel;

class Brand extends BaseModel
{
    public $uploadPath = 'uploads/brands';

    public function products() {
        return $this->hasMany(Product::class)->where('status', 'active');
    }
}
