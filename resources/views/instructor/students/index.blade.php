<x-layouts.app title="Daftar siswa">
    <x-ui.page-header
        title="Daftar siswa"
        description="Siswa yang mengikuti kursus Anda."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Siswa' => null]"
    />

    <x-ui.filter-bar :action="route('instructor.students.index')">
        <x-ui.search-input :query="request('q')" placeholder="Cari nama atau email" />
        <x-ui.button type="submit" variant="outline">Cari</x-ui.button>
    </x-ui.filter-bar>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>Siswa</th><th>Kursus</th><th>Progres</th><th>Terdaftar</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <x-ui.avatar :name="$enrollment->user?->name ?? 'Siswa'" size="sm" />
                                        <div>
                                            <div class="text-sm font-medium">{{ $enrollment->user?->name }}</div>
                                            <div class="text-muted-foreground text-xs">{{ $enrollment->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted-foreground">{{ $enrollment->course?->title }}</td>
                                <td>
                                    <x-ui.progress :value="$enrollment->progress_percent" />
                                </td>
                                <td class="text-muted-foreground">{{ $enrollment->enrolled_at?->translatedFormat('d M Y') }}</td>
                                <td>
                                    <x-ui.badge :variant="$enrollment->isCompleted() ? 'success' : 'neutral'">
                                        {{ $enrollment->isCompleted() ? 'Selesai' : 'Berjalan' }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-ui.empty-state icon="users" title="Belum ada siswa" description="Siswa akan muncul setelah mendaftar di kursus Anda." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $enrollments->links() }}</div>
</x-layouts.app>
