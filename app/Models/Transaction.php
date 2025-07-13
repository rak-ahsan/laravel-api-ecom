<?php

namespace App\Models;

use App\Classes\BaseModel;

class Transaction extends BaseModel
{
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
