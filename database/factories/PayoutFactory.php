<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payout>
 */
class PayoutFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'instructor_id' => User::factory()->instructor(),
            'amount' => fake()->randomElement([500000, 1000000, 2500000]),
            'status' => PayoutStatus::Requested,
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'Mandiri', 'BRI']),
            'bank_account_name' => fake()->name(),
            'bank_account_number' => fake()->numerify('##########'),
            'requested_at' => now(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayoutStatus::Paid,
            'approved_at' => now()->subDay(),
            'paid_at' => now(),
        ]);
    }
}
