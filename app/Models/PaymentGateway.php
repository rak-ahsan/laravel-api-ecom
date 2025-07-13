<?php

namespace App\Models;


use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGateway extends BaseModel
{
    public $uploadPath = 'uploads/paymentGateways';

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, "category_id", 'id');
    }
}
