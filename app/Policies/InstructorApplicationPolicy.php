<?php

namespace App\Policies;

use App\Models\InstructorApplication;
use App\Models\User;

class InstructorApplicationPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isInstructor() && ! $user->isAdmin();
    }

    public function view(User $user, InstructorApplication $application): bool
    {
        return $user->isAdmin() || $application->user_id === $user->getKey();
    }

    public function review(User $user): bool
    {
        return $user->isAdmin();
    }
}
