<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        RawMaterial::insert([
            [
                "name"      => "Raw material 1",
                "slug"      => "raw-material-1",
                "unit_cost" => 10,
                "quantity"  => 10,
                "total"     => 100,
            ],
            [
                "name"      => "Raw material 2",
                "slug"      => "raw-material-2",
                "unit_cost" => 5,
                "quantity"  => 20,
                "total"     => 100,
            ]
        ]);
    }
}
