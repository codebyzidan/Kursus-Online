<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PayoutStatus;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Carbon;

class InstructorEarningsService
{
    /** Total pendapatan instruktur dari pesanan yang sudah dibayar. */
    public function totalEarnings(User $instructor): int
    {
        return (int) OrderItem::query()
            ->where('instructor_id', $instructor->getKey())
            ->whereHas('order', fn ($query) => $query->where('status', OrderStatus::Paid->value))
            ->sum('instructor_earning');
    }

    /** Jumlah dana yang masih boleh dicairkan (pendapatan dikurangi payout terjadwal). */
    public function availableBalance(User $instructor): int
    {
        $reserved = (int) Payout::query()
            ->where('instructor_id', $instructor->getKey())
            ->whereIn('status', [
                PayoutStatus::Requested->value,
                PayoutStatus::Approved->value,
                PayoutStatus::Paid->value,
            ])
            ->sum('amount');

        return max(0, $this->totalEarnings($instructor) - $reserved);
    }

    /** Pendapatan pada rentang tanggal tertentu. */
    public function earningsBetween(User $instructor, Carbon $from, Carbon $to): int
    {
        return (int) OrderItem::query()
            ->where('instructor_id', $instructor->getKey())
            ->whereHas('order', fn ($query) => $query
                ->where('status', OrderStatus::Paid->value)
                ->whereBetween('paid_at', [$from, $to]))
            ->sum('instructor_earning');
    }
}
