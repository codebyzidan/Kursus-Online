<?php

use App\Actions\Course\ApproveCourse;
use App\Actions\Course\RejectCourse;
use App\Actions\Course\SubmitCourseForReview;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

it('menolak pengajuan kursus tanpa materi', function () {
    Notification::fake();

    $course = Course::factory()->create();

    app(SubmitCourseForReview::class)->handle($course);
})->throws(ValidationException::class);

it('mengubah status kursus menjadi menunggu review', function () {
    Notification::fake();

    $course = Course::factory()->create();
    $section = Section::create(['course_id' => $course->getKey(), 'title' => 'Bagian 1', 'position' => 1]);
    Lesson::factory()->create(['section_id' => $section->getKey()]);

    $course = app(SubmitCourseForReview::class)->handle($course);

    expect($course->status)->toBe(CourseStatus::PendingReview);
});

it('menerbitkan kursus saat disetujui', function () {
    Notification::fake();

    $course = Course::factory()->create();
    $section = Section::create(['course_id' => $course->getKey(), 'title' => 'Bagian 1', 'position' => 1]);
    Lesson::factory()->create(['section_id' => $section->getKey()]);

    $course = app(ApproveCourse::class)->handle($course);

    expect($course->status)->toBe(CourseStatus::Published)
        ->and($course->published_at)->not->toBeNull()
        ->and($course->lessons_count)->toBe(1);
});

it('menolak kursus beserta alasannya', function () {
    Notification::fake();

    $course = Course::factory()->create();

    $course = app(RejectCourse::class)->handle($course, 'Materi video belum lengkap.');

    expect($course->status)->toBe(CourseStatus::Rejected)
        ->and($course->rejection_reason)->toBe('Materi video belum lengkap.');
});
