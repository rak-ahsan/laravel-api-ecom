<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends BaseModel
{
    public function campaignProducts() : HasMany
    {
        return $this->hasMany(CampaignProduct::class, "campaign_id", "id");
    }
}
