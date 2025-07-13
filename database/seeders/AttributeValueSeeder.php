<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        AttributeValue::insert([
            [
                "id"           => 1,
                "attribute_id" => 1,
                "value"        => "M"
            ],
            [
                "id"           => 2,
                "attribute_id" => 1,
                "value"        => "L"
            ],
            [
                "id"           => 3,
                "attribute_id" => 1,
                "value"        => "XL"
            ],
            [
                "id"           => 4,
                "attribute_id" => 1,
                "value"        => "XLL"
            ],
            [
                "id"           => 5,
                "attribute_id" => 2,
                "value"        => "Red"
            ],
            [
                "id"           => 6,
                "attribute_id" => 2,
                "value"        => "Green"
            ],
            [
                "id"           => 7,
                "attribute_id" => 2,
                "value"        => "Blue"
            ],
            [
                "id"           => 8,
                "attribute_id" => 3,
                "value"        => "1 KG"
            ],
            [
                "id"           => 9,
                "attribute_id" => 3,
                "value"        => "2 KG"
            ],
            [
                "id"           => 10,
                "attribute_id" => 3,
                "value"        => "3 KG"
            ],
        ]);
    }
}
