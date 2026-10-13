<x-layouts.app :title="'Hasil kuis'">
    <x-ui.page-header
        title="Hasil kuis"
        :description="$quiz->title"
        :items="['Dashboard' => route('dashboard'), $course->title => route('student.learning.show', $course), 'Kuis' => route('student.quizzes.index', $course), 'Hasil' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('student.quizzes.show', [$course, $quiz])" variant="outline">Kerjakan lagi</x-ui.button>
            <x-ui.button :href="route('student.learning.show', $course)">Kembali ke kursus</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="card">
        <section class="flex flex-col items-center gap-3 p-8 text-center">
            @if ($attempt->passed)
                <span class="bg-success-soft text-success flex size-14 items-center justify-center rounded-full">
                    <x-lucide-check class="size-7" />
                </span>
                <h2 class="text-lg font-semibold">Selamat, Anda lulus!</h2>
            @else
                <span class="bg-danger-soft text-danger flex size-14 items-center justify-center rounded-full">
                    <x-lucide-x class="size-7" />
                </span>
                <h2 class="text-lg font-semibold">Belum lulus</h2>
            @endif

            <div class="text-4xl font-semibold tabular-nums tracking-tight">{{ $attempt->score }}</div>
            <p class="text-muted-foreground text-sm">Nilai lulus {{ $quiz->passing_grade }}</p>
        </section>
    </div>

    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Pembahasan</h2>

            @foreach ($quiz->questions as $question)
                @php
                    $selectedId = (int) ($answers[$question->getKey()] ?? $answers[(string) $question->getKey()] ?? 0);
                    $correct = $question->options->firstWhere('is_correct', true);
                    $isCorrect = $correct !== null && $selectedId === $correct->getKey();
                @endphp

                <article class="grid gap-2 border-b border-border pb-4 last:border-b-0 last:pb-0">
                    <div class="flex items-start gap-2">
                        @if ($isCorrect)
                            <x-lucide-circle-check class="mt-0.5 size-4 shrink-0 text-success" />
                        @else
                            <x-lucide-circle-x class="mt-0.5 size-4 shrink-0 text-danger" />
                        @endif
                        <h3 class="text-sm font-medium">{{ $question->text }}</h3>
                    </div>

                    <ul class="ms-6 grid gap-1 text-sm">
                        @foreach ($question->options as $option)
                            <li @class([
                                'flex items-center gap-2 rounded-md px-2 py-1',
                                'bg-success-soft text-success' => $correct?->getKey() === $option->getKey(),
                                'bg-danger-soft text-danger' => $selectedId === $option->getKey() && $correct?->getKey() !== $option->getKey(),
                            ])>
                                <span>{{ $option->text }}</span>
                                @if ($selectedId === $option->getKey())<span class="ms-auto text-xs">Jawaban Anda</span>@endif
                            </li>
                        @endforeach
                    </ul>

                    @if ($question->explanation)
                        <p class="text-muted-foreground ms-6 text-xs">Penjelasan: {{ $question->explanation }}</p>
                    @endif
                </article>
            @endforeach
        </section>
    </div>
</x-layouts.app>
