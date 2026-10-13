<?php

namespace App\Policies;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function view(?User $user, Course $course): bool
    {
        if ($course->isPublished()) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->isAdmin() || $course->instructor_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->instructor_id === $user->getKey();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->instructor_id === $user->getKey();
    }

    /** Instruktur boleh mengajukan kursus draft/ditolak untuk direview. */
    public function submitForReview(User $user, Course $course): bool
    {
        return $course->instructor_id === $user->getKey()
            && in_array($course->status, [CourseStatus::Draft, CourseStatus::Rejected], true);
    }

    /** Hanya admin yang boleh menyetujui/menolak kursus. */
    public function review(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Akses ruang belajar: pemilik kursus, admin, atau siswa terdaftar. */
    public function learn(?User $user, Course $course): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->isAdmin() || $course->instructor_id === $user->getKey()) {
            return true;
        }

        return $user->hasEnrolled($course);
    }
}
