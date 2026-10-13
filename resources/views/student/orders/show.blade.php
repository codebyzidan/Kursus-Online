<x-layouts.app :title="'Pesanan '.$order->code">
    <x-ui.page-header
        :title="'Pesanan '.$order->code"
        :description="$order->created_at?->translatedFormat('d M Y H:i')"
        :items="['Dashboard' => route('dashboard'), 'Pesanan' => route('student.orders.index'), $order->code => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('student.orders.invoice', $order)" variant="outline" target="_blank">
                <x-lucide-file-text class="size-4" data-icon="inline-start" /> Invoice
            </x-ui.button>
            @if ($order->isPending())
                <x-ui.button :href="route('checkout.show', $order)">Bayar sekarang</x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Item pesanan</h2>

                <ul class="grid gap-3">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                            <div class="bg-muted flex size-10 items-center justify-center rounded-md">
                                <x-lucide-book-open class="text-muted-foreground size-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">{{ $item->course_title }}</div>
                                @if ($item->course)
                                    <a href="{{ route('courses.show', $item->course) }}" class="text-muted-foreground text-xs hover:underline">Lihat kursus</a>
                                @endif
                            </div>
                            <div class="text-sm tabular-nums">Rp{{ number_format($item->net_price, 0, ',', '.') }}</div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div class="card">
            <section class="grid gap-3 p-5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">Status</span>
                    <x-ui.badge :variant="$order->isPaid() ? 'success' : 'warning'">{{ $order->status->label() }}</x-ui.badge>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="tabular-nums">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-muted-foreground">Diskon</span>
                    <span class="tabular-nums">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-border pt-3 font-medium">
                    <span>Total</span>
                    <span class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                @if ($order->coupon)
                    <div class="text-muted-foreground border-t border-border pt-3 text-xs">
                        Kupon: <span class="text-foreground font-medium">{{ $order->coupon->code }}</span>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.app>
