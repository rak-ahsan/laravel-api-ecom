<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::insert([
            [
                "name"         => "Supplier 1",
                "phone_number" => "012000000000",
                "email"        => "supplier1@gmail.com",
                "status"       => "active",
                "address"      => "Test address",
            ],
            [
                "name"         => "Supplier 2",
                "phone_number" => "012000000001",
                "email"        => "supplier2@gmail.com",
                "status"       => "active",
                "address"      => "Test address",
            ],
            [
                "name"         => "Supplier 3",
                "phone_number" => "012000000003",
                "email"        => "supplier3@gmail.com",
                "status"       => "active",
                "address"      => "Test address",
            ]
        ]);
    }
}
