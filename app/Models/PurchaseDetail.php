<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function attributeValue1(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_1", "id");
    }

    public function attributeValue2(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_2", "id");
    }

    public function attributeValue3(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_3", "id");
    }
}
