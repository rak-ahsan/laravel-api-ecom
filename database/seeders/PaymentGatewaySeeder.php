<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::create([
            'id'         => 1,
            'name'       => "Cash on delivery",
            'slug'       => "cash-on-delivery",
            'status'     => 'active',
            'created_at' => now(),
        ]);
    }
}
