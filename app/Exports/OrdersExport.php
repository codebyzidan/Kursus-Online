<?php

namespace App\Exports;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly Carbon $from,
        private readonly Carbon $to,
    ) {}

    /**
     * @return Collection<int, Order>
     */
    public function collection(): Collection
    {
        return Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereBetween('paid_at', [$this->from, $this->to])
            ->with(['user', 'items'])
            ->orderBy('paid_at')
            ->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['Kode', 'Tanggal dibayar', 'Pembeli', 'Email', 'Jumlah kursus', 'Subtotal', 'Diskon', 'Total'];
    }

    /**
     * @param  Order  $order
     * @return list<mixed>
     */
    public function map($order): array
    {
        return [
            $order->code,
            $order->paid_at?->format('d M Y H:i'),
            $order->user?->name,
            $order->user?->email,
            $order->items->count(),
            $order->subtotal,
            $order->discount,
            $order->total,
        ];
    }
}
