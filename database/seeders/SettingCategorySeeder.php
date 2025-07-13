<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\SettingCategory;
use Illuminate\Database\Seeder;

class SettingCategorySeeder extends Seeder
{
    public function run(): void
    {
        SettingCategory::insert([
            [
                "name"   => "General",
                "slug"   => "general",
                "status" => StatusEnum::ACTIVE->value,
            ]
        ]);
    }
}
