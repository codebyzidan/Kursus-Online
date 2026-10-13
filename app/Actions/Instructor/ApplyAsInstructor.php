<?php

namespace App\Actions\Instructor;

use App\Enums\InstructorApplicationStatus;
use App\Models\InstructorApplication;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ApplyAsInstructor
{
    /**
     * Buat pengajuan menjadi instruktur.
     *
     * @param  array{headline?: string|null, motivation: string, expertise?: string|null, portfolio_url?: string|null}  $data
     *
     * @throws ValidationException
     */
    public function handle(User $user, array $data): InstructorApplication
    {
        if ($user->instructorApplication?->isPending()) {
            throw ValidationException::withMessages([
                'motivation' => 'Pengajuan Anda sedang ditinjau oleh admin.',
            ]);
        }

        return InstructorApplication::create([
            'user_id' => $user->getKey(),
            'headline' => $data['headline'] ?? null,
            'motivation' => $data['motivation'],
            'expertise' => $data['expertise'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'status' => InstructorApplicationStatus::Pending,
        ]);
    }
}
