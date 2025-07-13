<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignProductVariation extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function attributeValue1() : BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_1", "id");
    }

    public function attributeValue2() : BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_2", "id");
    }

    public function attributeValue3() : BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id_3", "id");
    }
}
