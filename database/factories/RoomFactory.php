<?php

namespace Database\Factories;

use App\Models\room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('Room-###'),
            'capacity' => $this->faker->numberBetween(1, 4),
            'price_cents' => $this->faker->numberBetween(10000, 500000),
            'floor_id' => \App\Models\Floor::inRandomOrder()->first()->id ?? null,
            'manager_id' => \App\Models\User::where('role', 'manager')->inRandomOrder()->first()->id ?? null,

        ];
    }
}
