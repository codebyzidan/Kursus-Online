<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Jobs\GenerateInvoicePdfJob;
use App\Notifications\OrderPaidNotification;

class SendOrderPaidNotification
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        $order->user?->notify(new OrderPaidNotification($order));

        GenerateInvoicePdfJob::dispatch($order);
    }
}
