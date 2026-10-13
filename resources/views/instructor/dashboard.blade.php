<x-layouts.app title="Dashboard instruktur">
    <x-ui.page-header
        title="Dashboard instruktur"
        description="Ringkasan performa kursus dan pendapatan Anda."
        :items="['Dashboard instruktur' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('instructor.courses.create')">
                <x-lucide-plus class="size-4" data-icon="inline-start" /> Kursus baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Total kursus" :value="$stats['courses']" icon="folder-kanban" />
        <x-ui.stat-card label="Kursus terbit" :value="$stats['published']" icon="badge-check" />
        <x-ui.stat-card label="Total siswa" :value="number_format($stats['students'], 0, ',', '.')" icon="users" />
        <x-ui.stat-card label="Rating rata-rata" :value="$stats['rating'] ?: '—'" icon="star" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Pendapatan 6 bulan terakhir</h2>
                <x-ui.chart :config="$chart" label="Pendapatan 6 bulan terakhir" />
            </section>
        </div>

        <div class="card">
            <section class="grid gap-4 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-medium">Pendaftar terbaru</h2>
                    <x-ui.button :href="route('instructor.students.index')" variant="link" size="sm">Lihat semua</x-ui.button>
                </div>

                @forelse ($recentEnrollments as $enrollment)
                    <div class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                        <x-ui.avatar :name="$enrollment->user?->name ?? 'Siswa'" size="sm" />
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-medium">{{ $enrollment->user?->name }}</div>
                            <div class="text-muted-foreground truncate text-xs">{{ $enrollment->course?->title }}</div>
                        </div>
                        <span class="text-muted-foreground text-xs">{{ $enrollment->enrolled_at?->translatedFormat('d M Y') }}</span>
                    </div>
                @empty
                    <p class="text-muted-foreground text-sm">Belum ada pendaftar.</p>
                @endforelse
            </section>
        </div>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-medium">Kursus Anda</h2>
                <x-ui.button :href="route('instructor.courses.index')" variant="link" size="sm">Kelola kursus</x-ui.button>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>Kursus</th><th>Status</th><th>Bagian</th><th>Siswa</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td class="font-medium">{{ $course->title }}</td>
                                <td><x-ui.badge :variant="match ($course->status) {
                                    \App\Enums\CourseStatus::Published => 'success',
                                    \App\Enums\CourseStatus::PendingReview => 'warning',
                                    \App\Enums\CourseStatus::Rejected => 'danger',
                                    default => 'neutral',
                                }">{{ $course->status->label() }}</x-ui.badge></td>
                                <td class="tabular-nums">{{ $course->sections_count ?? $course->sections()->count() }}</td>
                                <td class="tabular-nums">{{ $course->enrollments_count }}</td>
                                <td class="text-end">
                                    <x-ui.button :href="route('instructor.courses.edit', $course)" variant="ghost" size="sm">Kelola</x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-ui.empty-state icon="folder-kanban" title="Belum ada kursus" description="Buat kursus pertama Anda." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
