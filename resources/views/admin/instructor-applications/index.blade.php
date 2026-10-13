<x-layouts.app title="Pengajuan instruktur">
    <x-ui.page-header
        title="Pengajuan instruktur"
        description="Tinjau permintaan pengguna untuk menjadi instruktur."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Pengajuan instruktur' => null]"
    />

    <x-ui.filter-bar :action="route('admin.instructor-applications.index')">
        <x-ui.filter-field>
            <x-ui.select name="status" :options="collect(\App\Enums\InstructorApplicationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])" :selected="request('status')" placeholder="Semua status" />
        </x-ui.filter-field>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
    </x-ui.filter-bar>

    <div class="grid gap-4">
        @forelse ($applications as $application)
            <div class="card">
                <section class="grid gap-4 p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.avatar :name="$application->user?->name ?? 'Pengguna'" size="sm" />
                        <div>
                            <div class="text-sm font-medium">{{ $application->user?->name }}</div>
                            <div class="text-muted-foreground text-xs">{{ $application->user?->email }}</div>
                        </div>
                        <x-ui.badge :variant="match ($application->status) {
                            \App\Enums\InstructorApplicationStatus::Approved => 'success',
                            \App\Enums\InstructorApplicationStatus::Rejected => 'danger',
                            default => 'warning',
                        }" class="ms-auto">{{ $application->status->label() }}</x-ui.badge>
                    </div>

                    <div class="grid gap-2 text-sm">
                        @if ($application->headline)
                            <div><span class="text-muted-foreground text-xs">Headline:</span> {{ $application->headline }}</div>
                        @endif
                        @if ($application->expertise)
                            <div><span class="text-muted-foreground text-xs">Keahlian:</span> {{ $application->expertise }}</div>
                        @endif
                        @if ($application->portfolio_url)
                            <div><a href="{{ $application->portfolio_url }}" class="underline underline-offset-4" target="_blank" rel="noopener">Portofolio</a></div>
                        @endif
                        <div><span class="text-muted-foreground text-xs">Motivasi:</span> {{ $application->motivation }}</div>
                    </div>

                    @if ($application->isPending())
                        <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                            <form method="POST" action="{{ route('admin.instructor-applications.approve', $application) }}">
                                @csrf
                                <x-ui.button type="submit">Setujui</x-ui.button>
                            </form>

                            <form method="POST" action="{{ route('admin.instructor-applications.reject', $application) }}" class="flex flex-wrap items-end gap-3">
                                @csrf
                                <div class="min-w-0 flex-1 basis-64">
                                    <x-ui.input name="reason" label="Alasan penolakan" required />
                                </div>
                                <x-ui.button type="submit" variant="danger">Tolak</x-ui.button>
                            </form>
                        </div>
                    @endif
                </section>
            </div>
        @empty
            <x-ui.empty-state icon="user-plus" title="Tidak ada pengajuan" description="Belum ada pengajuan instruktur." />
        @endforelse
    </div>

    <div>{{ $applications->links() }}</div>
</x-layouts.app>
