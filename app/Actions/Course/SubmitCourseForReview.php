<?php

namespace App\Actions\Course;

use App\Enums\CourseStatus;
use App\Events\CourseSubmittedForReview;
use App\Models\Course;
use Illuminate\Validation\ValidationException;

class SubmitCourseForReview
{
    /**
     * Ajukan kursus draf/ditolak untuk direview admin.
     *
     * @throws ValidationException
     */
    public function handle(Course $course): Course
    {
        if ($course->sections()->count() === 0) {
            throw ValidationException::withMessages([
                'curriculum' => 'Tambahkan minimal satu bagian dan materi sebelum mengajukan kursus.',
            ]);
        }

        if ($course->lessons()->count() === 0) {
            throw ValidationException::withMessages([
                'curriculum' => 'Kursus harus memiliki minimal satu materi.',
            ]);
        }

        $course->forceFill([
            'status' => CourseStatus::PendingReview,
            'rejection_reason' => null,
        ])->save();

        CourseSubmittedForReview::dispatch($course);

        return $course;
    }
}
