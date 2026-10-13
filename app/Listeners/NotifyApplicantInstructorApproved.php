<?php

namespace App\Listeners;

use App\Events\InstructorApplicationApproved;
use App\Notifications\InstructorApplicationApprovedNotification;

class NotifyApplicantInstructorApproved
{
    public function handle(InstructorApplicationApproved $event): void
    {
        $event->application->user?->notify(
            new InstructorApplicationApprovedNotification($event->application),
        );
    }
}
