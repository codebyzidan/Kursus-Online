<x-layouts.app title="Pendapatan">
    <x-ui.page-header
        title="Pendapatan"
        description="Laporan pendapatan dari penjualan kursus Anda."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Pendapatan' => null]"
    >
        <x-slot:actions>
            <x-ui.filter-bar :action="route('instructor.earnings.index')">
                <x-ui.filter-field width="sm:w-40">
                    <x-ui.input name="from" type="date" label="Dari" :value="$from->toDateString()" />
                </x-ui.filter-field>
                <x-ui.filter-field width="sm:w-40">
                    <x-ui.input name="to" type="date" label="Sampai" :value="$to->toDateString()" />
                </x-ui.filter-field>
                <x-ui.button type="submit" variant="outline">Terapkan</x-ui.button>
            </x-ui.filter-bar>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat-card label="Total pendapatan" :value="'Rp'.number_format($stats['total'], 0, ',', '.')" icon="wallet" />
        <x-ui.stat-card label="Periode ini" :value="'Rp'.number_format($stats['period'], 0, ',', '.')" icon="calendar-range" />
        <x-ui.stat-card label="Saldo dapat dicairkan" :value="'Rp'.number_format($stats['balance'], 0, ',', '.')" icon="piggy-bank" />
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Pendapatan 6 bulan terakhir</h2>
            <x-ui.chart :config="$chart" label="Pendapatan bulanan" />
        </section>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Pendapatan per kursus</h2>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kursus</th><th>Siswa</th><th>Pendapatan</th></tr></thead>
                    <tbody>
                        @forelse ($perCourse as $course)
                            <tr>
                                <td class="font-medium">{{ $course->title }}</td>
                                <td class="tabular-nums">{{ $course->enrollments_count }}</td>
                                <td class="tabular-nums">Rp{{ number_format((int) $course->earnings_sum, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><x-ui.empty-state icon="wallet" title="Belum ada pendapatan" description="Pendapatan muncul setelah ada penjualan." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Transaksi terbaru</h2>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Pesanan</th><th>Pembeli</th><th>Kursus</th><th>Pendapatan</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse ($transactions as $order)
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="font-medium">{{ $order->code }}</td>
                                    <td class="text-muted-foreground">{{ $order->user?->name }}</td>
                                    <td class="text-muted-foreground">{{ $item->course_title }}</td>
                                    <td class="tabular-nums">Rp{{ number_format($item->instructor_earning, 0, ',', '.') }}</td>
                                    <td class="text-muted-foreground">{{ $order->paid_at?->translatedFormat('d M Y') }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="5"><p class="text-muted-foreground p-4 text-sm">Belum ada transaksi.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
