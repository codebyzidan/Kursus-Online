<?php

use App\Actions\Learning\RecordLessonProgress;
use App\Jobs\GenerateCertificatePdfJob;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();

    $this->student = User::factory()->student()->create();
    $this->course = Course::factory()->published()->create();
    $this->section = Section::create([
        'course_id' => $this->course->getKey(),
        'title' => 'Pengenalan',
        'position' => 1,
    ]);
    $this->lesson = Lesson::create([
        'section_id' => $this->section->getKey(),
        'title' => 'Materi 1',
        'type' => 'video',
        'duration' => 600,
        'position' => 1,
        'is_published' => true,
    ]);
    $this->enrollment = Enrollment::create([
        'user_id' => $this->student->getKey(),
        'course_id' => $this->course->getKey(),
        'source' => 'purchase',
    ]);
});

it('menyelesaikan kursus dan menerbitkan sertifikat saat semua materi selesai', function () {
    app(RecordLessonProgress::class)->handle($this->student, $this->lesson, completed: true);

    $enrollment = $this->enrollment->fresh();

    expect($enrollment->progress_percent)->toBe(100)
        ->and($enrollment->completed_at)->not->toBeNull()
        ->and($enrollment->last_lesson_id)->toBe($this->lesson->getKey());

    expect(Certificate::query()->where('enrollment_id', $enrollment->getKey())->exists())->toBeTrue();
    Queue::assertPushed(GenerateCertificatePdfJob::class);
});

it('menyimpan posisi terakhir tanpa menandai selesai', function () {
    app(RecordLessonProgress::class)->handle($this->student, $this->lesson, completed: false, position: 120);

    $progress = $this->student->lessonProgress()->first();

    expect($progress->last_position)->toBe(120)
        ->and($progress->completed_at)->toBeNull()
        ->and($this->enrollment->fresh()->progress_percent)->toBe(0);
});

it('menolak progres siswa yang belum terdaftar', function () {
    $other = User::factory()->student()->create();

    app(RecordLessonProgress::class)->handle($other, $this->lesson, completed: true);
})->throws(AuthorizationException::class);
