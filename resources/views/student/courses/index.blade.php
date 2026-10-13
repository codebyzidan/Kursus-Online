<x-layouts.app title="Kursus saya">
    <x-ui.page-header
        title="Kursus saya"
        description="Semua kursus yang sedang Anda ikuti."
        :items="['Dashboard' => route('dashboard'), 'Kursus saya' => null]"
    />

    @if ($enrollments->isEmpty())
        <x-ui.empty-state icon="book-open" title="Belum ada kursus" description="Kursus yang Anda beli akan muncul di sini.">
            <x-slot:action>
                <x-ui.button :href="route('courses.index')">Jelajahi katalog</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($enrollments as $enrollment)
                <article class="card">
                    <section class="grid gap-3 p-5">
                        <div class="flex items-center gap-2">
                            @if ($enrollment->course->category)
                                <x-ui.badge variant="neutral">{{ $enrollment->course->category->name }}</x-ui.badge>
                            @endif
                            <x-ui.badge :variant="$enrollment->isCompleted() ? 'success' : 'outline'">
                                {{ $enrollment->isCompleted() ? 'Selesai' : 'Berjalan' }}
                            </x-ui.badge>
                        </div>

                        <h2 class="text-sm leading-snug font-semibold">
                            <a href="{{ route('student.learning.show', $enrollment->course) }}" class="hover:underline">
                                {{ $enrollment->course->title }}
                            </a>
                        </h2>

                        <div class="text-muted-foreground flex items-center gap-2 text-xs">
                            <x-ui.avatar :name="$enrollment->course->instructor?->name ?? 'Instruktur'" size="sm" />
                            <span>{{ $enrollment->course->instructor?->name }}</span>
                        </div>

                        <x-ui.progress :value="$enrollment->progress_percent" label="Progres" />
                    </section>

                    <footer class="flex items-center justify-between gap-2 border-t border-border p-4">
                        <span class="text-muted-foreground text-xs">Terdaftar {{ $enrollment->enrolled_at?->translatedFormat('d M Y') }}</span>
                        <x-ui.button :href="route('student.learning.show', $enrollment->course)" size="sm">
                            {{ $enrollment->progress_percent > 0 ? 'Lanjutkan' : 'Mulai' }}
                        </x-ui.button>
                    </footer>
                </article>
            @endforeach
        </div>

        <div>{{ $enrollments->links() }}</div>
    @endif
</x-layouts.app>
