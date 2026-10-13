<x-layouts.app title="Pesanan">
    <x-ui.page-header
        title="Pesanan"
        description="Pantau transaksi dan status pembayaran."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Pesanan' => null]"
    />

    <x-ui.filter-bar :action="route('admin.orders.index')">
        <x-ui.search-input :query="request('q')" placeholder="Cari kode atau pembeli" />
        <x-ui.filter-field>
            <x-ui.select name="status" :options="collect(\App\Enums\OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])" :selected="request('status')" placeholder="Semua status" />
        </x-ui.filter-field>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
    </x-ui.filter-bar>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kode</th><th>Pembeli</th><th>Item</th><th>Total</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="font-medium">{{ $order->code }}</td>
                                <td class="text-muted-foreground">{{ $order->user?->name }}</td>
                                <td class="tabular-nums">{{ $order->items_count }}</td>
                                <td class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                <td><x-ui.badge :variant="match ($order->status) {
                                    \App\Enums\OrderStatus::Paid => 'success',
                                    \App\Enums\OrderStatus::Pending => 'warning',
                                    \App\Enums\OrderStatus::Refunded => 'info',
                                    default => 'danger',
                                }">{{ $order->status->label() }}</x-ui.badge></td>
                                <td class="text-muted-foreground">{{ $order->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="text-end"><x-ui.button :href="route('admin.orders.show', $order)" variant="ghost" size="sm">Detail</x-ui.button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7"><x-ui.empty-state icon="receipt-text" title="Tidak ada pesanan" description="Belum ada pesanan yang cocok dengan filter." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $orders->links() }}</div>
</x-layouts.app>
