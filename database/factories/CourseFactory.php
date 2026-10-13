<?php

namespace Database\Factories;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomElement([0, 99000, 149000, 199000, 249000, 399000]);

        return [
            'instructor_id' => User::factory()->instructor(),
            'category_id' => Category::factory(),
            'title' => ucfirst(fake()->unique()->words(4, true)),
            'subtitle' => fake()->sentence(8),
            'description' => fake()->paragraphs(3, true),
            'level' => fake()->randomElement(CourseLevel::cases()),
            'price' => $price,
            'sale_price' => $price > 0 && fake()->boolean(30) ? (int) ($price * 0.7) : null,
            'status' => CourseStatus::Draft,
            'outcomes' => fake()->sentences(4),
            'requirements' => fake()->sentences(3),
            'meta_description' => fake()->sentence(12),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CourseStatus::PendingReview,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'sale_price' => null,
        ]);
    }
}
