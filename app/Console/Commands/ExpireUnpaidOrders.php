<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Notifications\OrderExpiredNotification;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Tandai pesanan pending yang melewati batas waktu sebagai kedaluwarsa';

    public function handle(): int
    {
        $expired = 0;

        Order::query()
            ->where('status', OrderStatus::Pending)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->with('user')
            ->chunkById(100, function ($orders) use (&$expired): void {
                foreach ($orders as $order) {
                    $order->forceFill(['status' => OrderStatus::Expired])->save();

                    $order->user?->notify(new OrderExpiredNotification($order));

                    $expired++;
                }
            });

        $this->info("Pesanan kedaluwarsa: {$expired}");

        return self::SUCCESS;
    }
}
