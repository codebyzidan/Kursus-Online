<?php

use App\Actions\Instructor\ApplyAsInstructor;
use App\Actions\Instructor\ApproveInstructorApplication;
use App\Enums\InstructorApplicationStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

beforeEach(fn () => Notification::fake());

it('membuat pengajuan instruktur berstatus menunggu', function () {
    $student = User::factory()->student()->create();

    $application = app(ApplyAsInstructor::class)->handle($student, [
        'motivation' => 'Saya ingin berbagi ilmu pemrograman.',
        'expertise' => 'Backend, Laravel',
    ]);

    expect($application->status)->toBe(InstructorApplicationStatus::Pending)
        ->and($application->user_id)->toBe($student->getKey());
});

it('menyetujui pengajuan dan memberi role instruktur', function () {
    $student = User::factory()->student()->create();
    $admin = User::factory()->admin()->create();

    $application = app(ApplyAsInstructor::class)->handle($student, [
        'motivation' => 'Saya ingin mengajar.',
    ]);

    app(ApproveInstructorApplication::class)->handle($application, $admin);

    expect($application->fresh()->status)->toBe(InstructorApplicationStatus::Approved)
        ->and($student->fresh()->hasRole(UserRole::Instructor->value))->toBeTrue();
});

it('menolak pengajuan ganda yang masih menunggu', function () {
    $student = User::factory()->student()->create();

    app(ApplyAsInstructor::class)->handle($student, ['motivation' => 'Pertama']);
    app(ApplyAsInstructor::class)->handle($student->fresh(), ['motivation' => 'Kedua']);
})->throws(ValidationException::class);
