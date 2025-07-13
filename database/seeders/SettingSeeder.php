<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::insert([
            [
                "setting_category_id" => 1,
                "key"                 => "is_bonus_point_add",
                "value"               => 1,
                "status"              => StatusEnum::ACTIVE->value,
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "bonus_point_value",
                "value"               => 1000,
                "status"              => StatusEnum::ACTIVE->value,
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_login_required",
                "value"               => 1,
                "status"              => StatusEnum::ACTIVE->value,
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_stock_maintain",
                "value"               => 1,
                "status"              => StatusEnum::ACTIVE->value,
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_stock_maintain_with_direct_product",
                "value"               => 1,
                "status"              => StatusEnum::ACTIVE->value,
            ],
        ]);
    }
}
