<x-layouts.app title="Pembayaran berhasil">
    <div class="card">
        <section class="flex flex-col items-center gap-4 p-10 text-center">
            <span class="bg-success-soft text-success flex size-14 items-center justify-center rounded-full">
                <x-lucide-check class="size-7" />
            </span>

            <h1 class="text-lg font-semibold">Pembayaran berhasil</h1>
            <p class="text-muted-foreground max-w-md text-sm">
                Akses kursus Anda sudah aktif. Selamat belajar!
            </p>

            <div class="flex flex-wrap items-center justify-center gap-2">
                @foreach ($order->items as $item)
                    @if ($item->course)
                        <x-ui.button :href="route('student.learning.show', $item->course)">
                            Mulai belajar: {{ \Illuminate\Support\Str::limit($item->course_title, 30) }}
                        </x-ui.button>
                    @endif
                @endforeach
            </div>
        </section>
    </div>

    <div class="card">
        <section class="grid gap-3 p-5 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-muted-foreground">Kode pesanan</span>
                <span class="font-medium">{{ $order->code }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-muted-foreground">Total dibayar</span>
                <span class="font-medium tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <x-ui.button :href="route('student.orders.show', $order)" variant="outline" class="w-fit">Lihat detail pesanan</x-ui.button>
        </section>
    </div>
</x-layouts.app>
