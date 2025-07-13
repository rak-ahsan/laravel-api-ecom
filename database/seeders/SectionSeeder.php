<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\SectionProduct;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [];

        for ($i = 1; $i <= 2; $i++) {
            $sections[] = [
                "title"  => "Section $i",
                "status" => "active",
            ];
        }

        Section::insert($sections);

        $sectionId = 1;
        for ($i = 1; $i <= 20; $i++) {
            SectionProduct::create([
                "section_id" => $sectionId,
                "product_id" => $i
            ]);

            if ($i == 10) {
                $sectionId = 2;
            }
        }
    }
}
