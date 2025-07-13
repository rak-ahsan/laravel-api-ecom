<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignProduct extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function campaign() : BelongsTo
    {
        return $this->belongsTo(Campaign::class, "campaign_id", "id");
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function campaignProductVariations() : HasMany
    {
        return $this->hasMany(CampaignProductVariation::class, "campaign_product_id", "id");
    }
}
