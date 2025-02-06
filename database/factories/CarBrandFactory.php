<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarBrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
