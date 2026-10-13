<?php

namespace App\Listeners;

use App\Events\PayoutRequested;
use App\Models\User;
use App\Notifications\PayoutRequestedNotification;

class NotifyAdminsPayoutRequested
{
    public function handle(PayoutRequested $event): void
    {
        User::query()
            ->role('admin')
            ->each(fn (User $admin) => $admin->notify(new PayoutRequestedNotification($event->payout)));
    }
}
