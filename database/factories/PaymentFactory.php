<?php

namespace Database\Factories;

use App\Models\payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        
            'reservation_id' => \App\Models\reservation::inRandomOrder()->first()->id ?? null,
            'amount_cents' => $this->faker->numberBetween(1000, 100000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'stripe']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'transaction_id' => $this->faker->uuid(),
        ];
    }
}
