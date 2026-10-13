<?php

namespace App\Actions\Course;

use App\Enums\CourseStatus;
use App\Events\CourseApproved;
use App\Models\Course;

class ApproveCourse
{
    public function handle(Course $course): Course
    {
        $course->forceFill([
            'status' => CourseStatus::Published,
            'published_at' => $course->published_at ?? now(),
            'rejection_reason' => null,
        ])->save();

        $course->refreshCounters();

        CourseApproved::dispatch($course);

        return $course;
    }
}
