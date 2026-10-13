<x-layouts.app :title="'Pesanan '.$order->code">
    <x-ui.page-header
        :title="'Pesanan '.$order->code"
        :description="$order->user?->name.' · '.$order->created_at?->translatedFormat('d M Y H:i')"
        :items="['Dashboard admin' => route('admin.dashboard'), 'Pesanan' => route('admin.orders.index'), $order->code => null]"
    >
        <x-slot:actions>
            <x-ui.badge :variant="match ($order->status) {
                \App\Enums\OrderStatus::Paid => 'success',
                \App\Enums\OrderStatus::Pending => 'warning',
                \App\Enums\OrderStatus::Refunded => 'info',
                default => 'danger',
            }">{{ $order->status->label() }}</x-ui.badge>

            @if ($order->isPaid())
                <form method="POST" action="{{ route('admin.orders.refund', $order) }}" onsubmit="return confirm('Tandai pesanan ini sebagai dana dikembalikan?')">
                    @csrf
                    <x-ui.button type="submit" variant="outline">Kembalikan dana</x-ui.button>
                </form>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Item</h2>
                <ul class="grid gap-3">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">{{ $item->course_title }}</div>
                                <div class="text-muted-foreground text-xs">Instruktur: {{ $item->instructor?->name }}</div>
                            </div>
                            <div class="text-end text-sm">
                                <div class="tabular-nums">Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                @if ($item->discount > 0)
                                    <div class="text-muted-foreground text-xs tabular-nums">-Rp{{ number_format($item->discount, 0, ',', '.') }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div class="card">
            <section class="grid gap-3 p-5 text-sm">
                <div class="flex justify-between"><span class="text-muted-foreground">Pembeli</span><span>{{ $order->user?->name }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Email</span><span>{{ $order->user?->email }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Metode</span><span>{{ $order->payment_channel?->label() ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Dibayar</span><span>{{ $order->paid_at?->translatedFormat('d M Y H:i') ?? '—' }}</span></div>
                <div class="flex justify-between border-t border-border pt-3"><span class="text-muted-foreground">Subtotal</span><span class="tabular-nums">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-muted-foreground">Diskon</span><span class="tabular-nums">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-medium"><span>Total</span><span class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</span></div>
            </section>
        </div>
    </div>
</x-layouts.app>
