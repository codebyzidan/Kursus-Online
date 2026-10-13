<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $score = fake()->numberBetween(0, 100);

        return [
            'user_id' => User::factory()->student(),
            'quiz_id' => Quiz::factory(),
            'score' => $score,
            'passed' => $score >= 70,
            'answers' => [],
            'started_at' => now()->subMinutes(20),
            'finished_at' => now(),
        ];
    }
}
