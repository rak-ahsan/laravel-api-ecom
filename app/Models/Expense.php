<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends BaseModel
{

    public function category() : BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, "category_id", "id");
    }
}
