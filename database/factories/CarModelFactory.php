<?php

namespace Database\Factories;

use App\Models\CarBrand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'car_brand_id' => CarBrand::factory(),
        ];
    }
}
