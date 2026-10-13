<?php

namespace App\Listeners;

use App\Events\CourseApproved;
use App\Notifications\CourseApprovedNotification;

class NotifyInstructorCourseApproved
{
    public function handle(CourseApproved $event): void
    {
        $event->course->instructor?->notify(new CourseApprovedNotification($event->course));
    }
}
