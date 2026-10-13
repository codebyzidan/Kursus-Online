<?php

namespace Database\Factories;

use App\Enums\LessonType;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(LessonType::cases());

        return [
            'section_id' => Section::factory(),
            'title' => ucfirst(fake()->words(4, true)),
            'type' => $type,
            'content' => $type === LessonType::Text ? fake()->paragraphs(4, true) : null,
            'video_url' => $type === LessonType::Video ? 'https://video.example.test/'.fake()->uuid() : null,
            'video_provider' => $type === LessonType::Video ? 'self' : null,
            'duration' => fake()->numberBetween(120, 1800),
            'position' => fake()->numberBetween(1, 20),
            'is_preview' => fake()->boolean(15),
            'is_published' => true,
        ];
    }
}
