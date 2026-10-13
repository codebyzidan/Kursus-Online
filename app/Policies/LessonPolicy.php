<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function view(?User $user, Lesson $lesson): bool
    {
        if ($lesson->is_preview) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        $course = $lesson->section->course;

        if ($user->isAdmin() || $course->instructor_id === $user->getKey()) {
            return true;
        }

        return $user->hasEnrolled($course);
    }

    public function update(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;

        return $user->isAdmin() || $course->instructor_id === $user->getKey();
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $this->update($user, $lesson);
    }
}
