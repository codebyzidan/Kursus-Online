<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Certificate $certificate) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->certificate->enrollment->course;

        return (new MailMessage)
            ->subject('Sertifikat Anda telah terbit')
            ->markdown('mail.certificate-issued', [
                'certificate' => $this->certificate,
                'course' => $course,
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_issued',
            'certificate_number' => $this->certificate->number,
            'message' => 'Sertifikat '.$this->certificate->number.' telah terbit.',
        ];
    }
}
