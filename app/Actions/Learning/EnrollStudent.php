<?php

namespace App\Actions\Learning;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;

class EnrollStudent
{
    /**
     * Berikan akses kursus kepada siswa (idempotent).
     */
    public function handle(
        User $student,
        Course $course,
        ?Order $order = null,
        string $source = 'purchase',
    ): Enrollment {
        $enrollment = Enrollment::firstOrCreate(
            [
                'user_id' => $student->getKey(),
                'course_id' => $course->getKey(),
            ],
            [
                'order_id' => $order?->getKey(),
                'source' => $source,
                'enrolled_at' => now(),
            ],
        );

        if ($enrollment->wasRecentlyCreated) {
            $course->increment('students_count');
        }

        return $enrollment;
    }
}
