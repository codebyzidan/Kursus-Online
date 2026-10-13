<x-layouts.app title="Pencairan dana">
    <x-ui.page-header
        title="Pencairan dana"
        description="Ajukan pencairan dari saldo pendapatan Anda."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Pencairan dana' => null]"
    />

    <div class="grid gap-4 sm:grid-cols-2">
        <x-ui.stat-card label="Saldo dapat dicairkan" :value="'Rp'.number_format($balance, 0, ',', '.')" icon="piggy-bank" />
        <x-ui.stat-card label="Total pendapatan" :value="'Rp'.number_format($total, 0, ',', '.')" icon="wallet" />
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Riwayat pencairan</h2>
                <div class="table-container">
                    <table class="table">
                        <thead><tr><th>Tanggal</th><th>Nominal</th><th>Bank</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($payouts as $payout)
                                <tr>
                                    <td class="text-muted-foreground">{{ $payout->requested_at?->translatedFormat('d M Y') }}</td>
                                    <td class="tabular-nums">Rp{{ number_format($payout->amount, 0, ',', '.') }}</td>
                                    <td class="text-muted-foreground">{{ $payout->bank_name }} · {{ $payout->bank_account_number }}</td>
                                    <td><x-ui.badge :variant="match ($payout->status) {
                                        \App\Enums\PayoutStatus::Paid => 'success',
                                        \App\Enums\PayoutStatus::Approved => 'info',
                                        \App\Enums\PayoutStatus::Rejected => 'danger',
                                        default => 'warning',
                                    }">{{ $payout->status->label() }}</x-ui.badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><p class="text-muted-foreground p-4 text-sm">Belum ada pencairan.</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="card h-fit">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Ajukan pencairan</h2>

                <form method="POST" action="{{ route('instructor.payouts.store') }}" class="grid gap-4">
                    @csrf
                    <x-ui.input name="amount" type="number" label="Nominal (Rp)" :value="old('amount')" min="10000" :max="$balance" required hint="Maksimal Rp{{ number_format($balance, 0, ',', '.') }}" />
                    <x-ui.input name="bank_name" label="Nama bank" :value="old('bank_name')" required />
                    <x-ui.input name="bank_account_name" label="Nama pemilik rekening" :value="old('bank_account_name')" required />
                    <x-ui.input name="bank_account_number" label="Nomor rekening" :value="old('bank_account_number')" required />
                    <x-ui.textarea name="note" label="Catatan (opsional)" rows="2">{{ old('note') }}</x-ui.textarea>

                    <x-ui.button type="submit" class="w-full" :disabled="$balance < 10000">Kirim permintaan</x-ui.button>
                </form>
            </section>
        </div>
    </div>

    <div>{{ $payouts->links() }}</div>
</x-layouts.app>
