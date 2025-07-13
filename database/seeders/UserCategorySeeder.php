<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\UserCategory;
use Illuminate\Database\Seeder;

class UserCategorySeeder extends Seeder
{
    public function run(): void
    {
        UserCategory::insert([
            [
                "name" => "Customer",
                "slug" => "customer",
                "status" => StatusEnum::ACTIVE->value,
            ],
            [
                "name" => "Admin",
                "slug" => "admin",
                "status" => StatusEnum::ACTIVE->value,
            ],
            [
                "name" => "Employee",
                "slug" => "employee",
                "status" => StatusEnum::ACTIVE->value,
            ]
        ]);
    }
}
