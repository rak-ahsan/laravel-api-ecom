<?php

namespace Database\Seeders;

use App\Models\DeliveryGateway;
use Illuminate\Database\Seeder;

class DeliveryGatewaySeeder extends Seeder
{
    public function run(): void
    {
        DeliveryGateway::insert([
            [
                'name'         => "Dhaka",
                'slug'         => "dhaka",
                'delivery_fee' => 60,
                'min_time'     => 1,
                'max_time'     => 3,
                'time_unit'    => 'Days',
                'status'       => 'active',
                'created_at'   => now()
            ],
            [
                'name'         => "Others",
                'slug'         => "others",
                'delivery_fee' => 120,
                'min_time'     => 1,
                'max_time'     => 3,
                'time_unit'    => 'Days',
                'status'       => 'active',
                'created_at'   => now()
            ]
        ]);

    }
}
