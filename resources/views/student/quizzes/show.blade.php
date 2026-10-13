<x-layouts.app :title="$quiz->title">
    <x-ui.page-header
        :title="$quiz->title"
        :description="$quiz->description"
        :items="['Dashboard' => route('dashboard'), $course->title => route('student.learning.show', $course), 'Kuis' => route('student.quizzes.index', $course), $quiz->title => null]"
    >
        <x-slot:actions>
            <x-ui.badge variant="outline">Nilai lulus {{ $quiz->passing_grade }}</x-ui.badge>
            @if ($remaining !== null)
                <x-ui.badge variant="neutral">Sisa percobaan {{ $remaining }}</x-ui.badge>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    @if ($remaining === 0)
        <x-ui.alert variant="warning" title="Batas percobaan tercapai">
            Anda telah menggunakan seluruh kesempatan untuk kuis ini.
        </x-ui.alert>
    @else
        <form method="POST" action="{{ route('student.quizzes.submit', [$course, $quiz]) }}" class="grid gap-4">
            @csrf

            @foreach ($quiz->questions as $question)
                <div class="card">
                    <section class="grid gap-3 p-5">
                        <div class="flex items-start gap-2">
                            <span class="bg-muted text-muted-foreground flex size-6 shrink-0 items-center justify-center rounded-md text-xs font-medium">
                                {{ $loop->iteration }}
                            </span>
                            <h2 class="text-sm font-medium">{{ $question->text }}</h2>
                        </div>

                        <fieldset class="grid gap-2">
                            <legend class="sr-only">Pilih jawaban</legend>
                            @foreach ($question->options as $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-border p-3 text-sm hover:bg-muted">
                                    <input type="radio" name="answers[{{ $question->getKey() }}]" value="{{ $option->getKey() }}" required>
                                    <span>{{ $option->text }}</span>
                                </label>
                            @endforeach
                        </fieldset>
                    </section>
                </div>
            @endforeach

            <x-ui.button type="submit" size="lg" class="w-fit">Kumpulkan jawaban</x-ui.button>
        </form>
    @endif
</x-layouts.app>
