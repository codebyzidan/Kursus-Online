<x-layouts.app title="Kuis">
    <x-ui.page-header
        title="Kuis"
        description="Kelola kuis untuk kursus Anda."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kuis' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('instructor.quizzes.create')">
                <x-lucide-plus class="size-4" data-icon="inline-start" /> Kuis baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr><th>Kuis</th><th>Kursus</th><th>Soal</th><th>Percobaan</th><th>Nilai lulus</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($quizzes as $quiz)
                            <tr>
                                <td class="font-medium">
                                    {{ $quiz->title }}
                                    @if ($quiz->is_final)<x-ui.badge variant="primary" class="ms-2">Akhir</x-ui.badge>@endif
                                </td>
                                <td class="text-muted-foreground">{{ $quiz->course?->title }}</td>
                                <td class="tabular-nums">{{ $quiz->questions_count }}</td>
                                <td class="tabular-nums">{{ $quiz->attempts_count }}</td>
                                <td class="tabular-nums">{{ $quiz->passing_grade }}</td>
                                <td class="text-end">
                                    @if ($quiz->course)
                                        <x-ui.button :href="route('instructor.quizzes.edit', [$quiz->course, $quiz])" variant="ghost" size="sm">Kelola</x-ui.button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="list-checks" title="Belum ada kuis" description="Buat kuis untuk menguji pemahaman siswa.">
                                <x-slot:action><x-ui.button :href="route('instructor.quizzes.create')">Kuis baru</x-ui.button></x-slot:action>
                            </x-ui.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $quizzes->links() }}</div>
</x-layouts.app>
