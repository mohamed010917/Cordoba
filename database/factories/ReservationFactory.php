<?php

namespace Database\Factories;

use App\Models\reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
          
                'client_id' => \App\Models\User::where('role', 'user')->inRandomOrder()->first()->id ?? null,
                'room_id' => \App\Models\Room::inRandomOrder()->first()->id ?? null,
                'accompany_number' => $this->faker->numberBetween(0, 5),
                'paid_price_cents' => $this->faker->numberBetween(10000, 500000),
                'receptionist_id' => \App\Models\User::where('role', 'receptionist')->inRandomOrder()->first()->id ?? null,
        ];
    }
}
