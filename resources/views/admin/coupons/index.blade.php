<x-layouts.app title="Kupon">
    <x-ui.page-header
        title="Kupon"
        description="Kelola kode diskon untuk pembelian kursus."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Kupon' => null]"
    />

    <div class="card">
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @csrf
            <section class="grid gap-4 p-5 sm:grid-cols-4">
                <x-ui.input name="code" label="Kode" required placeholder="HEMAT20" />
                <x-ui.select name="type" label="Tipe" :options="collect(\App\Enums\CouponType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])" :selected="'percent'" required />
                <x-ui.input name="value" type="number" label="Nilai" required min="1" hint="Persen atau nominal." />
                <x-ui.input name="max_discount" type="number" label="Maks diskon (Rp)" min="1" />
                <x-ui.input name="min_purchase" type="number" label="Min pembelian (Rp)" :value="0" min="0" />
                <x-ui.input name="quota" type="number" label="Kuota" min="1" />
                <x-ui.input name="valid_until" type="date" label="Berlaku sampai" />
                <x-ui.checkbox name="is_active" label="Aktif" :checked="true" />
                <div class="sm:col-span-4">
                    <x-ui.button type="submit">Buat kupon</x-ui.button>
                </div>
            </section>
        </form>
    </div>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kode</th><th>Tipe</th><th>Nilai</th><th>Terpakai</th><th>Berlaku sampai</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td class="font-medium">{{ $coupon->code }}</td>
                                <td class="text-muted-foreground">{{ $coupon->type->label() }}</td>
                                <td class="tabular-nums">{{ $coupon->type === \App\Enums\CouponType::Percent ? $coupon->value.'%' : 'Rp'.number_format($coupon->value, 0, ',', '.') }}</td>
                                <td class="tabular-nums">{{ $coupon->used }}@if ($coupon->quota)/{{ $coupon->quota }}@endif</td>
                                <td class="text-muted-foreground">{{ $coupon->valid_until?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td><x-ui.badge :variant="$coupon->isValid() ? 'success' : 'neutral'">{{ $coupon->isValid() ? 'Berlaku' : 'Tidak berlaku' }}</x-ui.badge></td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Hapus kupon {{ $coupon->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="ghost" size="icon-sm" aria-label="Hapus kupon"><x-lucide-trash-2 class="size-4" /></x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><x-ui.empty-state icon="ticket-percent" title="Belum ada kupon" description="Buat kupon pertama untuk mengadakan diskon." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $coupons->links() }}</div>
</x-layouts.app>
