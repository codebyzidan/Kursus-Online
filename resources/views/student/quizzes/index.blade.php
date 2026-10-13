<x-layouts.app title="Kuis kursus">
    <x-ui.page-header
        title="Kuis: {{ $course->title }}"
        description="Kerjakan kuis untuk menguji pemahaman Anda."
        :items="['Dashboard' => route('dashboard'), 'Kursus saya' => route('student.courses.index'), $course->title => route('student.learning.show', $course), 'Kuis' => null]"
    />

    @if ($quizzes->isEmpty())
        <x-ui.empty-state icon="list-checks" title="Belum ada kuis" description="Instruktur belum menambahkan kuis pada kursus ini." />
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($quizzes as $quiz)
                @php $attempts = $attempts->get($quiz->getKey(), collect()); $best = $attempts->max('score'); @endphp
                <article class="card">
                    <section class="grid gap-3 p-5">
                        <div class="flex items-center gap-2">
                            <x-ui.badge :variant="$quiz->is_final ? 'primary' : 'neutral'">
                                {{ $quiz->is_final ? 'Kuis akhir' : 'Kuis' }}
                            </x-ui.badge>
                            @if ($attempts->isNotEmpty())
                                <x-ui.badge :variant="$best >= $quiz->passing_grade ? 'success' : 'warning'">
                                    Nilai terbaik {{ $best }}
                                </x-ui.badge>
                            @endif
                        </div>

                        <h2 class="font-medium">{{ $quiz->title }}</h2>

                        <dl class="text-muted-foreground grid grid-cols-3 gap-3 text-xs">
                            <div>
                                <dt>Soal</dt>
                                <dd class="text-foreground font-medium">{{ $quiz->questions_count }}</dd>
                            </div>
                            <div>
                                <dt>Nilai lulus</dt>
                                <dd class="text-foreground font-medium">{{ $quiz->passing_grade }}</dd>
                            </div>
                            <div>
                                <dt>Percobaan</dt>
                                <dd class="text-foreground font-medium">
                                    {{ $attempts->count() }}@if ($quiz->max_attempts) / {{ $quiz->max_attempts }}@endif
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <footer class="border-t border-border p-4">
                        <x-ui.button :href="route('student.quizzes.show', [$course, $quiz])" class="w-full">
                            {{ $attempts->isEmpty() ? 'Mulai kuis' : 'Kerjakan lagi' }}
                        </x-ui.button>
                    </footer>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.app>
