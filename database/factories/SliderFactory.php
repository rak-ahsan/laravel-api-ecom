<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Slider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slider>
 */
class SliderFactory extends Factory
{
     protected $model = Slider::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image  = $this->faker->numberBetween(1, 4) . ".png";

        return [
            'title'    => $this->faker->name,
            'status'   => $this->faker->randomElement(['active', 'inactive']),
            'img_path' => "uploads/sliders/" . $image,
        ];
    }
}
