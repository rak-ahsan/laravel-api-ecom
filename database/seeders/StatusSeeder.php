<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            [
                'id'         => 1,
                'slug'       => 'pending',
                'name'       => 'Pending',
                'bg_color'   => '#ddb063',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 2,
                'slug'       => 'on-hold',
                'name'       => 'On Hold',
                'bg_color'   => '#C98209',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 3,
                'slug'       => 'approved',
                'name'       => 'Approved',
                'bg_color'   => '#06d14a',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 4,
                'slug'       => 'picked',
                'name'       => 'Picked',
                'bg_color'   => '#CDDC39',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 5,
                'slug'       => 'canceled',
                'name'       => 'Canceled',
                'bg_color'   => '#F44336',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 6,
                'slug'       => 'on-way',
                'name'       => 'On Way',
                'bg_color'   => '#673AB7',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 7,
                'slug'       => 'invoiced',
                'name'       => 'Invoiced',
                'bg_color'   => '#673AB7',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 8,
                'slug'       => 'completed',
                'name'       => 'Completed',
                'bg_color'   => '#4CAF50',
                'text_color' => '#ffffff'
            ],
            [
                'id'         => 9,
                'slug'       => 'returned',
                'name'       => 'Returned',
                'bg_color'   => '#9C27B0',
                'text_color' => '#ffffff'
            ],
        ]);
    }
}
