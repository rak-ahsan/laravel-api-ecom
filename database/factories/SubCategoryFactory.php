<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image =  $this->faker->numberBetween(1, 5) . ".png";
         return [
            // 'image' => $this->faker->imageUrl('1440', '450'),
            'category_id' => Category::inRandomOrder()->first()->id,
            'image' => $image,
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'status' => 'active',
            'is_top' => 1,
        ];
    }
}
