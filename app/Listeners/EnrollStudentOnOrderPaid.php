<?php

namespace App\Listeners;

use App\Actions\Learning\EnrollStudent;
use App\Events\OrderPaid;

class EnrollStudentOnOrderPaid
{
    public function __construct(private readonly EnrollStudent $enrollStudent) {}

    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        foreach ($order->items as $item) {
            $course = $item->course;

            if ($course === null) {
                continue;
            }

            $this->enrollStudent->handle($order->user, $course, $order, 'purchase');
        }
    }
}
