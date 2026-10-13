<?php

namespace App\Listeners;

use App\Events\CourseRejected;
use App\Notifications\CourseRejectedNotification;

class NotifyInstructorCourseRejected
{
    public function handle(CourseRejected $event): void
    {
        $event->course->instructor?->notify(new CourseRejectedNotification($event->course, $event->reason));
    }
}
