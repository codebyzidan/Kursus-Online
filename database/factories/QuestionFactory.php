<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'text' => rtrim(fake()->sentence(), '.').'?',
            'explanation' => fake()->sentence(),
            'points' => 1,
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
