<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payout $payout) {}

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
            ->subject('Permintaan penarikan dana baru')
            ->markdown('mail.payout-requested', [
                'payout' => $this->payout,
                'instructor' => $this->payout->instructor,
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payout_requested',
            'payout_id' => $this->payout->getKey(),
            'amount' => $this->payout->amount,
            'message' => 'Instruktur '.$this->payout->instructor->name.' mengajukan penarikan dana.',
        ];
    }
}
