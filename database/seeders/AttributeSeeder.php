<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attribute::insert([
            [
                "id"     => 1,
                "name"   => "Size",
                "slug"   => "size",
                "status" => StatusEnum::ACTIVE->value
            ],
            [
                "id"     => 2,
                "name"   => "Color",
                "slug"   => "color",
                "status" => StatusEnum::ACTIVE->value
            ],
            [
                "id"     => 3,
                "name"   => "Weight",
                "slug"   => "weight",
                "status" => StatusEnum::ACTIVE->value
            ]
        ]);
    }
}
