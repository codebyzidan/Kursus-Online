<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonQuestion>
 */
class LessonQuestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory()->student(),
            'parent_id' => null,
            'body' => rtrim(fake()->sentence(), '.').'?',
            'answered_at' => null,
        ];
    }

    public function answered(): static
    {
        return $this->state(fn (array $attributes) => ['answered_at' => now()]);
    }
}
