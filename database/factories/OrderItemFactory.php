<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomElement([99000, 149000, 199000, 249000]);

        return [
            'order_id' => Order::factory(),
            'course_id' => Course::factory(),
            'instructor_id' => fn (array $attributes) => Course::find($attributes['course_id'])?->instructor_id,
            'course_title' => ucfirst(fake()->words(4, true)),
            'price' => $price,
            'discount' => 0,
            'instructor_earning' => (int) ($price * 0.7),
        ];
    }
}
