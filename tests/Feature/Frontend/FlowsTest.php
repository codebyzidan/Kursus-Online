<?php

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Notification::fake();
    Queue::fake();
});

it('siswa mengikuti kursus gratis lewat checkout', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->free()->create();

    $this->actingAs($student)
        ->post(route('checkout.store'), ['courses' => [$course->getKey()]])
        ->assertRedirect();

    expect($student->fresh()->hasEnrolled($course))->toBeTrue();
});

it('instruktur membuat kursus, kurikulum, lalu mengajukan review', function () {
    $instructor = User::factory()->instructor()->create();
    $category = Category::factory()->create();

    $this->actingAs($instructor)->post(route('instructor.courses.store'), [
        'title' => 'Kursus Uji Instruktur',
        'level' => 'beginner',
        'price' => 100000,
        'category_id' => $category->getKey(),
    ])->assertRedirect();

    $course = Course::query()->where('title', 'Kursus Uji Instruktur')->firstOrFail();
    expect($course->status)->toBe(CourseStatus::Draft);

    $this->actingAs($instructor)
        ->post(route('instructor.sections.store', $course), ['title' => 'Bagian 1'])
        ->assertRedirect();

    $section = $course->sections()->firstOrFail();

    $this->actingAs($instructor)->post(route('instructor.lessons.store', [$course, $section]), [
        'title' => 'Materi 1',
        'type' => 'text',
        'content' => 'Isi materi',
        'is_published' => 1,
    ])->assertRedirect();

    $this->actingAs($instructor)
        ->post(route('instructor.courses.submit', $course))
        ->assertRedirect();

    expect($course->fresh()->status)->toBe(CourseStatus::PendingReview);
});

it('admin menyetujui kursus menjadi terbit', function () {
    $admin = User::factory()->admin()->create();
    $course = Course::factory()->pendingReview()->create();

    $this->actingAs($admin)
        ->post(route('admin.courses.approve', $course))
        ->assertRedirect();

    expect($course->fresh()->status)->toBe(CourseStatus::Published);
});

it('siswa menandai materi selesai lewat HTTP', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $section = Section::create(['course_id' => $course->getKey(), 'title' => 'Bagian 1', 'position' => 1]);
    $lesson = Lesson::create([
        'section_id' => $section->getKey(),
        'title' => 'Materi 1',
        'type' => 'text',
        'content' => 'Isi',
        'position' => 1,
        'is_published' => true,
    ]);
    Enrollment::create([
        'user_id' => $student->getKey(),
        'course_id' => $course->getKey(),
        'source' => 'purchase',
    ]);

    $this->actingAs($student)
        ->post(route('student.learning.progress', [$course, $lesson]), ['completed' => 1])
        ->assertRedirect();

    expect($student->fresh()->enrollmentFor($course)->progress_percent)->toBe(100);
});

it('menolak akses ruang belajar bagi siswa yang belum terdaftar', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();

    $this->actingAs($student)
        ->get(route('student.learning.show', $course))
        ->assertForbidden();
});
