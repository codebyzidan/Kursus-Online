<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Course $course) {}

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
            ->subject('Kursus disetujui: '.$this->course->title)
            ->markdown('mail.course-approved', [
                'course' => $this->course,
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_approved',
            'course_id' => $this->course->getKey(),
            'message' => 'Kursus "'.$this->course->title.'" telah disetujui dan tayang di katalog.',
        ];
    }
}
