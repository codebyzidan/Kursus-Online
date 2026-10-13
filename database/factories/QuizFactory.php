<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => 'Kuis '.ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(),
            'passing_grade' => 70,
            'max_attempts' => 3,
            'duration_minutes' => null,
            'is_final' => false,
            'is_published' => true,
            'position' => 1,
        ];
    }

    public function final(): static
    {
        return $this->state(fn (array $attributes) => ['is_final' => true]);
    }
}
