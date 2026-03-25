<?php

namespace Database\Factories;

use App\Models\countrie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<countrie>
 */
class CountrieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'code' => fake()->unique()->countryCode(),
        ];
    }
}
