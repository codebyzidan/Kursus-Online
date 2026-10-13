<x-layouts.app title="Review kursus">
    <x-ui.page-header
        title="Review kursus"
        description="Setujui atau tolak kursus yang diajukan instruktur."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Review kursus' => null]"
    />

    <x-ui.filter-bar :action="route('admin.courses.index')">
        <x-ui.search-input :query="request('q')" placeholder="Cari judul kursus" />
        <x-ui.filter-field>
            <x-ui.select name="status" :options="collect(\App\Enums\CourseStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])" :selected="request('status')" placeholder="Semua status" />
        </x-ui.filter-field>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
    </x-ui.filter-bar>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Kursus</th><th>Instruktur</th><th>Konten</th><th>Harga</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td class="font-medium">{{ $course->title }}</td>
                                <td class="text-muted-foreground">{{ $course->instructor?->name }}</td>
                                <td class="text-muted-foreground tabular-nums">{{ $course->sections_count }} bagian · {{ $course->lessons_count }} materi</td>
                                <td class="tabular-nums">{{ $course->isFree() ? 'Gratis' : 'Rp'.number_format($course->getEffectivePriceAttribute(), 0, ',', '.') }}</td>
                                <td><x-ui.badge :variant="match ($course->status) {
                                    \App\Enums\CourseStatus::Published => 'success',
                                    \App\Enums\CourseStatus::PendingReview => 'warning',
                                    \App\Enums\CourseStatus::Rejected => 'danger',
                                    default => 'neutral',
                                }">{{ $course->status->label() }}</x-ui.badge></td>
                                <td class="text-end">
                                    <x-ui.button :href="route('admin.courses.show', $course)" variant="ghost" size="sm">Tinjau</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="clipboard-check" title="Tidak ada kursus" description="Belum ada kursus yang cocok dengan filter." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $courses->links() }}</div>
</x-layouts.app>
