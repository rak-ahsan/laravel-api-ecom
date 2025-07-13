<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            SliderSeeder::class,
            SectionSeeder::class,
            LaratrustSeeder::class,
            DeliveryGatewaySeeder::class,
            PaymentGatewaySeeder::class,
            StatusSeeder::class,
            UserCategorySeeder::class,
            AdminSeeder::class,
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            ProductVariationSeeder::class,
            SupplierSeeder::class,
            RawMaterialSeeder::class,
            SettingCategorySeeder::class,
            SettingSeeder::class,
        ]);
    }
}
