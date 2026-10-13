<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;

it('mengizinkan siswa mengulas setelah progres mencukupi', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    Enrollment::factory()->create([
        'user_id' => $student->getKey(),
        'course_id' => $course->getKey(),
        'progress_percent' => 50,
    ]);

    expect($student->can('create', [Review::class, $course]))->toBeTrue();
});

it('menolak siswa yang belum cukup progres', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    Enrollment::factory()->create([
        'user_id' => $student->getKey(),
        'course_id' => $course->getKey(),
        'progress_percent' => 5,
    ]);

    expect($student->can('create', [Review::class, $course]))->toBeFalse();
});

it('mengizinkan hanya siswa terdaftar mengakses ruang belajar', function () {
    $student = User::factory()->student()->create();
    $course = Course::factory()->published()->create();

    expect($student->can('learn', $course))->toBeFalse();

    Enrollment::factory()->create([
        'user_id' => $student->getKey(),
        'course_id' => $course->getKey(),
    ]);

    expect($student->fresh()->can('learn', $course))->toBeTrue();
});

it('memberi admin akses penuh atas semua kursus', function () {
    $admin = User::factory()->admin()->create();
    $draft = Course::factory()->create();

    expect($admin->can('update', $draft))->toBeTrue()
        ->and($admin->can('review', Course::class))->toBeTrue();
});
