<?php

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function create(User $user): bool
    {
        return $user->isInstructor();
    }

    public function view(User $user, Payout $payout): bool
    {
        return $user->isAdmin() || $payout->instructor_id === $user->getKey();
    }

    public function update(User $user, Payout $payout): bool
    {
        return $user->isAdmin();
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
