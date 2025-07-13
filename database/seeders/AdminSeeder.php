<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       DB::table('users')->insert([
            [
                "user_category_id" => 2,
                'username'         => 'admin',
                'phone_number'     => '01686381998',
                'email'            => 'admin@gmail.com',
                'status'           => StatusEnum::ACTIVE->value,
                'password'         => Hash::make('123456789'),
            ],
            [
                "user_category_id" => 2,
                'username'         => 'superadmin',
                'phone_number'     => '01764997485',
                'email'            => 'superadmin@gmail.com',
                'status'           => StatusEnum::ACTIVE->value,
                'password'         => Hash::make('123456789'),
            ]
        ]);

        DB::table('role_user')->insert([
            [
                'role_id'   => 1,
                'user_id'   => 1,
                'user_type' => 'App\Models\User'
            ],
            [
                'role_id'   => 1,
                'user_id'   => 2,
                'user_type' => 'App\Models\User'
            ]
        ]);


    }
}
