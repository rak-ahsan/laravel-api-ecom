<?php

namespace App\Models;

use App\Classes\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends BaseModel
{
    public $uploadPath = 'uploads/settings';

    public function settingCategory() : BelongsTo
    {
        return $this->belongsTo(SettingCategory::class, "setting_category_id", "id");
    }

}
