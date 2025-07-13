<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mrp      = $this->faker->numberBetween(200, 3000);
        $discount = rand(2, 30);

        // Introduce a variable to decide whether to include offer_price
        $includeOfferPrice = $this->faker->boolean(50); // Adjust probability as needed

        $offerPrice = $includeOfferPrice ? ($mrp - (($mrp * $discount) / 100)) : 0;
        $offer_percent = $includeOfferPrice ? (($mrp - $offerPrice) / $mrp) * 100 : 0;

        $types         = ['recent-product', 'feature-product', 'top-product'];
        $image         = $this->faker->numberBetween(1, 14) . ".png";
        $name          = $this->faker->name;
        return [
            'name'             => $name,
            'slug'             => Str::slug($name),
            'description'      => $this->faker->words(rand(80, 300), true),
            'short_description'=> $this->faker->words(rand(10, 100), true),
            'brand_id'         => rand(1, 10),
            'category_id'      => rand(1, 10),
            'buy_price'        => 150,
            'mrp'              => $mrp,
            'offer_price'      => $offerPrice,
            'sell_price'       => $offerPrice > 0 ? $offerPrice : $mrp,
            'discount'         => $discount,
            'offer_percent'    => $offer_percent,
            'current_stock'    => rand(0, 100),
            'status'           => 'active',
            'img_path'         => "uploads/products/" . $image,
            'type'             => $types[rand(0, 2)],
        ];
    }
}
