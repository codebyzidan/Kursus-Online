<?php

namespace App\Actions\Instructor;

use App\Enums\InstructorApplicationStatus;
use App\Enums\UserRole;
use App\Events\InstructorApplicationApproved;
use App\Models\InstructorApplication;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ApproveInstructorApplication
{
    public function handle(InstructorApplication $application, User $reviewer): InstructorApplication
    {
        $application->forceFill([
            'status' => InstructorApplicationStatus::Approved,
            'rejection_reason' => null,
            'reviewed_by' => $reviewer->getKey(),
            'reviewed_at' => now(),
        ])->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $application->user->assignRole(
            Role::findOrCreate(UserRole::Instructor->value, 'web'),
        );

        InstructorApplicationApproved::dispatch($application);

        return $application;
    }
}
