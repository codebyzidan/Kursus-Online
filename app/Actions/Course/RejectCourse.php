<?php

namespace App\Actions\Course;

use App\Enums\CourseStatus;
use App\Events\CourseRejected;
use App\Models\Course;

class RejectCourse
{
    public function handle(Course $course, string $reason): Course
    {
        $course->forceFill([
            'status' => CourseStatus::Rejected,
            'rejection_reason' => $reason,
        ])->save();

        CourseRejected::dispatch($course, $reason);

        return $course;
    }
}
