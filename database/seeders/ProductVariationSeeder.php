<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariation;

class ProductVariationSeeder extends Seeder
{
    public function run(): void
    {
        ProductVariation::insert([
            // product 1 variations
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 5,
                "attribute_value_id_3" => 8,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 500,
                "mrp"                  => 800,
                "offer_price"          => 720,
                "sell_price"           => 720,
                "discount"             => 80,
                "offer_percent"        => 10,
            ],
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 6,
                "attribute_value_id_3" => 9,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 600,
                "mrp"                  => 1000,
                "offer_price"          => 0,
                "sell_price"           => 1000,
                "discount"             => 0,
                "offer_percent"        => 0,
            ],
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 7,
                "attribute_value_id_3" => 10,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 700,
                "mrp"                  => 900,
                "offer_price"          => 820,
                "sell_price"           => 820,
                "discount"             => 90,
                "offer_percent"        => 10,
            ],
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 2,
                "attribute_value_id_2" => 5,
                "attribute_value_id_3" => 8,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 450,
                "mrp"                  => 600,
                "offer_price"          => 0,
                "sell_price"           => 600,
                "discount"             => 0,
                "offer_percent"        => 0,
            ],

            // product 1 variations

            [
                "product_id"           => 2,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 5,
                "attribute_value_id_3" => 8,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 500,
                "mrp"                  => 800,
                "offer_price"          => 720,
                "sell_price"           => 720,
                "discount"             => 80,
                "offer_percent"        => 10,
            ],
            [
                "product_id"           => 2,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 6,
                "attribute_value_id_3" => 9,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 600,
                "mrp"                  => 1000,
                "offer_price"          => 0,
                "sell_price"           => 1000,
                "discount"             => 0,
                "offer_percent"        => 0,
            ],
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 1,
                "attribute_value_id_2" => 7,
                "attribute_value_id_3" => 10,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 700,
                "mrp"                  => 900,
                "offer_price"          => 820,
                "sell_price"           => 820,
                "discount"             => 90,
                "offer_percent"        => 10,
            ],
            [
                "product_id"           => 1,
                "attribute_value_id_1" => 2,
                "attribute_value_id_2" => 5,
                "attribute_value_id_3" => 8,
                "current_stock"        => 100,
                "is_default"           => 1,
                "buy_price"            => 450,
                "mrp"                  => 600,
                "offer_price"          => 0,
                "sell_price"           => 600,
                "discount"             => 0,
                "offer_percent"        => 0,
            ]
        ]);
    }
}
