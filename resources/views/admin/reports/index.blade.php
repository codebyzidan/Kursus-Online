<x-layouts.app title="Laporan">
    <x-ui.page-header
        title="Laporan"
        description="Ringkasan penjualan dan ekspor data."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Laporan' => null]"
    >
        <x-slot:actions>
            <x-ui.filter-bar :action="route('admin.reports.index')">
                <x-ui.filter-field width="sm:w-40">
                    <x-ui.input name="from" type="date" label="Dari" :value="$from->toDateString()" />
                </x-ui.filter-field>
                <x-ui.filter-field width="sm:w-40">
                    <x-ui.input name="to" type="date" label="Sampai" :value="$to->toDateString()" />
                </x-ui.filter-field>
                <x-ui.button type="submit" variant="outline">Terapkan</x-ui.button>
            </x-ui.filter-bar>
            <x-ui.button :href="route('admin.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()])">
                <x-lucide-download class="size-4" data-icon="inline-start" /> Ekspor Excel
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Jumlah pesanan" :value="number_format($summary['orders'], 0, ',', '.')" icon="receipt-text" />
        <x-ui.stat-card label="Total pendapatan" :value="'Rp'.number_format($summary['revenue'], 0, ',', '.')" icon="wallet" />
        <x-ui.stat-card label="Rata-rata per pesanan" :value="'Rp'.number_format($summary['average'], 0, ',', '.')" icon="trending-up" />
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Pendapatan harian</h2>
            <x-ui.chart :config="$chart" label="Pendapatan harian" />
        </section>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Kursus terlaris</h2>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kursus</th><th>Instruktur</th><th>Siswa</th><th>Rating</th></tr></thead>
                    <tbody>
                        @forelse ($topCourses as $course)
                            <tr>
                                <td class="font-medium">{{ $course->title }}</td>
                                <td class="text-muted-foreground">{{ $course->instructor?->name }}</td>
                                <td class="tabular-nums">{{ $course->students_count }}</td>
                                <td class="tabular-nums">{{ number_format((float) $course->rating_avg, 1) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><p class="text-muted-foreground p-4 text-sm">Belum ada data.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
