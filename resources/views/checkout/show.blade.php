<x-layouts.app :title="'Pembayaran '.$order->code">
    <x-ui.page-header
        :title="'Pembayaran '.$order->code"
        description="Selesaikan pembayaran untuk mengaktifkan akses kursus."
        :items="['Pesanan' => route('student.orders.index'), $order->code => null]"
    >
        <x-slot:actions>
            <x-ui.badge :variant="$order->isPaid() ? 'success' : 'warning'">{{ $order->status->label() }}</x-ui.badge>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Rincian pembayaran</h2>

                @if (config('services.midtrans.client_key') && $order->snap_token)
                    <p class="text-muted-foreground text-sm">
                        Klik tombol di bawah untuk memilih metode pembayaran (Virtual Account, e-wallet, QRIS, atau kartu).
                        Setelah pembayaran berhasil, akses kursus aktif otomatis.
                    </p>

                    <x-ui.button type="button" size="lg" class="w-fit" onclick="payWithSnap()">
                        <x-lucide-credit-card class="size-4" data-icon="inline-start" /> Bayar sekarang
                    </x-ui.button>

                    @if ($order->expired_at)
                        <p class="text-muted-foreground text-xs">Selesaikan sebelum {{ $order->expired_at->translatedFormat('d M Y H:i') }}.</p>
                    @endif

                    <script
                        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                        data-client-key="{{ config('services.midtrans.client_key') }}"
                    ></script>
                    <script>
                        function payWithSnap() {
                            window.snap.pay(@js($order->snap_token), {
                                onSuccess: function () { window.location.href = @js(route('checkout.success', $order)); },
                                onPending: function () { window.location.href = @js(route('student.orders.show', $order)); },
                            });
                        }
                    </script>
                @else
                    <x-ui.alert variant="warning" title="Pembayaran belum dikonfigurasi">
                        Kredensial gateway belum diatur, sehingga pembayaran online belum dapat diproses.
                        Pesanan tetap tercatat dan dapat dibayar setelah admin mengatur kredensial Midtrans.
                    </x-ui.alert>
                @endif
            </section>
        </div>

        <div class="card h-fit">
            <section class="grid gap-3 p-5 text-sm">
                <h2 class="font-medium">Ringkasan</h2>
                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground min-w-0 truncate">{{ $item->course_title }}</span>
                        <span class="tabular-nums">Rp{{ number_format($item->net_price, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex items-center justify-between border-t border-border pt-3 font-medium">
                    <span>Total</span>
                    <span class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <x-ui.button :href="route('student.orders.show', $order)" variant="outline" class="w-full">Lihat pesanan</x-ui.button>
            </section>
        </div>
    </div>
</x-layouts.app>
