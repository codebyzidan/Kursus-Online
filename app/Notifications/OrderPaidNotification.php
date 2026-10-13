<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

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
            ->subject('Pembayaran diterima — '.$this->order->code)
            ->markdown('mail.order-paid', [
                'order' => $this->order,
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_paid',
            'order_code' => $this->order->code,
            'total' => $this->order->total,
            'message' => 'Pembayaran untuk pesanan '.$this->order->code.' berhasil. Selamat belajar!',
        ];
    }
}
