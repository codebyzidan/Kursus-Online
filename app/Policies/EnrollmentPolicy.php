<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->isAdmin()
            || $enrollment->user_id === $user->getKey()
            || $enrollment->course->instructor_id === $user->getKey();
    }

    public function learn(User $user, Enrollment $enrollment): bool
    {
        return $enrollment->user_id === $user->getKey();
    }
}
