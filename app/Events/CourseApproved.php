<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public Course $course) {}
}
