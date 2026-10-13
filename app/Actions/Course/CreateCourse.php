<?php

namespace App\Actions\Course;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;

class CreateCourse
{
    /**
     * Buat kursus baru berstatus draf untuk seorang instruktur.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $instructor, array $data): Course
    {
        return Course::create([
            'instructor_id' => $instructor->getKey(),
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'description' => $data['description'] ?? null,
            'level' => $data['level'] ?? 'beginner',
            'price' => $data['price'] ?? 0,
            'status' => CourseStatus::Draft,
        ]);
    }
}
