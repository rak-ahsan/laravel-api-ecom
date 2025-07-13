<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FAQ extends BaseModel
{
    use HasFactory;
    protected $table = "faqs";
}
