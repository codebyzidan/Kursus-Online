<?php

namespace App\Notifications;

use App\Models\InstructorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorApplicationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InstructorApplication $application) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat, Anda kini menjadi instruktur')
            ->markdown('mail.instructor-approved', [
                'application' => $this->application,
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'instructor_approved',
            'message' => 'Pengajuan instruktur Anda disetujui. Anda sudah bisa membuat kursus.',
        ];
    }
}
