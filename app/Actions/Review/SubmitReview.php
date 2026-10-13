<?php

namespace App\Actions\Review;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;

class SubmitReview
{
    public function handle(User $student, Course $course, int $rating, ?string $body = null): Review
    {
        return Review::updateOrCreate(
            [
                'user_id' => $student->getKey(),
                'course_id' => $course->getKey(),
            ],
            [
                'rating' => $rating,
                'body' => $body,
                'is_approved' => false,
                'approved_at' => null,
                'instructor_reply' => null,
                'replied_at' => null,
            ],
        );
    }
}
