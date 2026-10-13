<x-layouts.app title="Riwayat pesanan">
    <x-ui.page-header
        title="Riwayat pesanan"
        description="Daftar pembelian dan status pembayaran Anda."
        :items="['Dashboard' => route('dashboard'), 'Pesanan' => null]"
    />

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Jumlah kursus</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="font-medium">{{ $order->code }}</td>
                                <td class="text-muted-foreground">{{ $order->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="tabular-nums">{{ $order->items_count }}</td>
                                <td class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <x-ui.badge :variant="match ($order->status) {
                                        \App\Enums\OrderStatus::Paid => 'success',
                                        \App\Enums\OrderStatus::Pending => 'warning',
                                        \App\Enums\OrderStatus::Refunded => 'info',
                                        default => 'danger',
                                    }">{{ $order->status->label() }}</x-ui.badge>
                                </td>
                                <td class="text-end">
                                    <x-ui.button :href="route('student.orders.show', $order)" variant="ghost" size="sm">Detail</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state icon="receipt" title="Belum ada pesanan" description="Pembelian kursus Anda akan tampil di sini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $orders->links() }}</div>
</x-layouts.app>
