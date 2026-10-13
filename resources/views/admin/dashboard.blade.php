<x-layouts.app title="Dashboard admin">
    <x-ui.page-header
        title="Dashboard admin"
        description="Ringkasan operasional dan keuangan platform."
        :items="['Dashboard admin' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('admin.reports.index')" variant="outline">
                <x-lucide-chart-column class="size-4" data-icon="inline-start" /> Laporan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Total pengguna" :value="number_format($stats['users'], 0, ',', '.')" icon="users-round" :delta="'+'.$stats['newThisMonth'].' bulan ini'" direction="up" deltaLabel="" />
        <x-ui.stat-card label="Instruktur" :value="number_format($stats['instructors'], 0, ',', '.')" icon="presentation" />
        <x-ui.stat-card label="Kursus" :value="number_format($stats['courses'], 0, ',', '.')" icon="library" />
        <x-ui.stat-card label="Menunggu review" :value="$stats['pending']" icon="clipboard-check" />
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Pendapatan" :value="'Rp'.number_format($stats['revenue'], 0, ',', '.')" icon="wallet" />
        <x-ui.stat-card label="Siswa" :value="number_format($stats['students'], 0, ',', '.')" icon="graduation-cap" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Pendapatan 6 bulan terakhir</h2>
                <x-ui.chart :config="$revenueChart" label="Pendapatan bulanan" />
            </section>
        </div>

        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Kursus terlaris</h2>
                <ul class="grid gap-3">
                    @forelse ($topCourses as $course)
                        <li class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                            <span class="bg-muted text-muted-foreground flex size-6 items-center justify-center rounded-md text-xs font-medium">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">{{ $course->title }}</div>
                                <div class="text-muted-foreground text-xs">{{ $course->instructor?->name }}</div>
                            </div>
                            <span class="text-muted-foreground text-xs tabular-nums">{{ $course->students_count }} siswa</span>
                        </li>
                    @empty
                        <p class="text-muted-foreground text-sm">Belum ada data.</p>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-medium">Pesanan terbaru</h2>
                <x-ui.button :href="route('admin.orders.index')" variant="link" size="sm">Lihat semua</x-ui.button>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kode</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="font-medium">{{ $order->code }}</td>
                                <td class="text-muted-foreground">{{ $order->user?->name }}</td>
                                <td class="tabular-nums">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                <td><x-ui.badge :variant="$order->isPaid() ? 'success' : 'warning'">{{ $order->status->label() }}</x-ui.badge></td>
                                <td class="text-muted-foreground">{{ $order->created_at?->translatedFormat('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><p class="text-muted-foreground p-4 text-sm">Belum ada pesanan.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
