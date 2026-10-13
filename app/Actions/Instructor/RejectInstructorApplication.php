<?php

namespace App\Actions\Instructor;

use App\Enums\InstructorApplicationStatus;
use App\Models\InstructorApplication;
use App\Models\User;

class RejectInstructorApplication
{
    public function handle(InstructorApplication $application, User $reviewer, string $reason): InstructorApplication
    {
        $application->forceFill([
            'status' => InstructorApplicationStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer->getKey(),
            'reviewed_at' => now(),
        ])->save();

        return $application;
    }
}
