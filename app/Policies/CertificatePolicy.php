<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function view(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin()
            || $certificate->enrollment->user_id === $user->getKey();
    }

    public function download(User $user, Certificate $certificate): bool
    {
        return $this->view($user, $certificate);
    }
}
