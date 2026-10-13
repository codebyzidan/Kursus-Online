<?php

namespace App\Actions\Learning;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class RecordLessonProgress
{
    public function __construct(private readonly CompleteCourse $completeCourse) {}

    /**
     * Catat progres sebuah materi, perbarui kemajuan kursus, dan terbitkan
     * sertifikat bila kursus selesai.
     *
     * @throws AuthorizationException
     */
    public function handle(
        User $student,
        Lesson $lesson,
        bool $completed = false,
        ?int $position = null,
    ): LessonProgress {
        $course = $lesson->section->course;

        $enrollment = $student->enrollmentFor($course);

        if ($enrollment === null) {
            throw new AuthorizationException('Anda belum terdaftar pada kursus ini.');
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $student->getKey(),
            'lesson_id' => $lesson->getKey(),
        ]);

        $progress->course_id = $course->getKey();

        if ($position !== null) {
            $progress->last_position = $position;
        }

        if ($completed && $progress->completed_at === null) {
            $progress->completed_at = now();
        }

        $progress->save();

        $enrollment->forceFill(['last_lesson_id' => $lesson->getKey()])->save();
        $enrollment->updateProgress();

        if ($enrollment->isCompleted()) {
            $this->completeCourse->handle($enrollment);
        }

        return $progress;
    }
}
