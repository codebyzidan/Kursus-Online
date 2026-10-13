<x-layouts.app title="Kelola kursus">
    <x-ui.page-header
        title="Kelola kursus"
        description="Daftar kursus yang Anda buat beserta statusnya."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kelola kursus' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('instructor.courses.create')">
                <x-lucide-plus class="size-4" data-icon="inline-start" /> Kursus baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>Kursus</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Siswa</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $course->title }}</div>
                                    <div class="text-muted-foreground text-xs">{{ $course->lessons_count ?? 0 }} materi</div>
                                </td>
                                <td class="text-muted-foreground">{{ $course->category?->name ?? '—' }}</td>
                                <td class="tabular-nums">{{ $course->isFree() ? 'Gratis' : 'Rp'.number_format($course->getEffectivePriceAttribute(), 0, ',', '.') }}</td>
                                <td>
                                    <x-ui.badge :variant="match ($course->status) {
                                        \App\Enums\CourseStatus::Published => 'success',
                                        \App\Enums\CourseStatus::PendingReview => 'warning',
                                        \App\Enums\CourseStatus::Rejected => 'danger',
                                        default => 'neutral',
                                    }">{{ $course->status->label() }}</x-ui.badge>
                                </td>
                                <td class="tabular-nums">{{ $course->enrollments_count }}</td>
                                <td class="text-end">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button :href="route('instructor.courses.curriculum', $course)" variant="outline" size="sm">Kurikulum</x-ui.button>
                                        <x-ui.button :href="route('instructor.courses.edit', $course)" variant="ghost" size="sm">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="folder-kanban" title="Belum ada kursus" description="Mulai dengan membuat kursus pertama.">
                                <x-slot:action><x-ui.button :href="route('instructor.courses.create')">Kursus baru</x-ui.button></x-slot:action>
                            </x-ui.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $courses->links() }}</div>
</x-layouts.app>
