<x-layouts.app title="Checkout">
    <x-ui.page-header
        title="Checkout"
        description="Tinjau pesanan Anda sebelum melanjutkan pembayaran."
        :items="['Katalog' => route('courses.index'), 'Checkout' => null]"
    />

    @if ($courses->isEmpty())
        <x-ui.empty-state icon="shopping-cart" title="Tidak ada kursus dipilih" description="Pilih kursus terlebih dahulu dari katalog.">
            <x-slot:action>
                <x-ui.button :href="route('courses.index')">Jelajahi katalog</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="card">
                <section class="grid gap-4 p-5">
                    <h2 class="font-medium">Kursus yang dibeli</h2>
                    <ul class="grid gap-3">
                        @foreach ($courses as $course)
                            <li class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                                <div class="bg-muted flex size-10 items-center justify-center rounded-md">
                                    <x-lucide-book-open class="text-muted-foreground size-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-sm font-medium">{{ $course->title }}</div>
                                    <div class="text-muted-foreground text-xs">{{ $course->instructor?->name }}</div>
                                </div>
                                <div class="text-sm tabular-nums">
                                    {{ $course->isFree() ? 'Gratis' : 'Rp'.number_format($course->getEffectivePriceAttribute(), 0, ',', '.') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <div class="card h-fit lg:sticky lg:top-20">
                <form method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    @foreach ($courses as $course)
                        <input type="hidden" name="courses[]" value="{{ $course->getKey() }}">
                    @endforeach

                    <section class="grid gap-4 p-5">
                        <x-ui.input name="coupon" label="Kode kupon (opsional)" :value="old('coupon')" placeholder="Contoh: MULAI50" />

                        <div class="grid gap-2 border-t border-border pt-4 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Subtotal</span>
                                <span class="tabular-nums">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-muted-foreground text-xs">Diskon dihitung setelah kupon divalidasi saat pesanan dibuat.</p>
                        </div>

                        <x-ui.button type="submit" size="lg" class="w-full">
                            {{ $subtotal === 0 ? 'Ikuti kursus gratis' : 'Buat pesanan' }}
                        </x-ui.button>
                    </section>
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>
