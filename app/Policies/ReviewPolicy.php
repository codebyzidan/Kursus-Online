<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /** Siswa boleh mengulas kursus yang diikuti dan progresnya cukup. */
    public function create(User $user, Course $course): bool
    {
        $enrollment = $user->enrollmentFor($course);

        if ($enrollment === null) {
            return false;
        }

        return $enrollment->progress_percent >= (int) config('kursus.review_min_progress', 30);
    }

    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->getKey() && ! $review->is_approved;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->user_id === $user->getKey();
    }

    /** Instruktur pemilik kursus boleh membalas ulasan. */
    public function reply(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->course->instructor_id === $user->getKey();
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }
}
