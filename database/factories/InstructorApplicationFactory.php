<?php

namespace Database\Factories;

use App\Enums\InstructorApplicationStatus;
use App\Models\InstructorApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstructorApplication>
 */
class InstructorApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'headline' => fake()->jobTitle(),
            'motivation' => fake()->paragraph(),
            'expertise' => implode(', ', fake()->words(4)),
            'portfolio_url' => fake()->url(),
            'status' => InstructorApplicationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InstructorApplicationStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }
}
