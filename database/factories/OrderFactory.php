<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomElement([99000, 149000, 199000, 249000]);

        return [
            'user_id' => User::factory()->student(),
            'subtotal' => $amount,
            'discount' => 0,
            'total' => $amount,
            'status' => OrderStatus::Pending,
            'expired_at' => now()->addHour(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'gateway_ref' => fake()->uuid(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Expired,
            'expired_at' => now()->subHour(),
        ]);
    }
}
