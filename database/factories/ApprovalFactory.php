<?php

namespace Database\Factories;

use App\Models\approval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<approval>
 */
class ApprovalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                'user_id' => \App\Models\User::inRandomOrder()->first()->id ?? null,
                'approved_by' => \App\Models\User::where('role', 'admin')->inRandomOrder()->first()->id ?? null,
                'approved_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'banned_by' => \App\Models\User::where('role', 'admin')->inRandomOrder()->first()->id ?? null,
                'banned_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
        ];
    }
}
