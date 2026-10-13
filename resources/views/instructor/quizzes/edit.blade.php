<x-layouts.app :title="'Kuis: '.$quiz->title">
    <x-ui.page-header
        :title="$quiz->title"
        :description="$course->title"
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kuis' => route('instructor.quizzes.index'), 'Kelola' => null]"
    >
        <x-slot:actions>
            <x-ui.badge variant="outline">Nilai lulus {{ $quiz->passing_grade }}</x-ui.badge>
            <x-ui.badge variant="neutral">{{ $quiz->questions->count() }} soal</x-ui.badge>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Pengaturan kuis --}}
    <form method="POST" action="{{ route('instructor.quizzes.update', [$course, $quiz]) }}" class="card">
        <section class="grid gap-5 p-5">
            @csrf
            @method('PATCH')

            <x-ui.input name="title" label="Judul kuis" :value="old('title', $quiz->title)" required />
            <x-ui.textarea name="description" label="Deskripsi" rows="3">{{ old('description', $quiz->description) }}</x-ui.textarea>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.input name="passing_grade" type="number" label="Nilai lulus" :value="old('passing_grade', $quiz->passing_grade)" min="0" max="100" required />
                <x-ui.input name="max_attempts" type="number" label="Batas percobaan" :value="old('max_attempts', $quiz->max_attempts)" min="1" />
                <x-ui.input name="duration_minutes" type="number" label="Durasi (menit)" :value="old('duration_minutes', $quiz->duration_minutes)" min="1" />
            </div>

            <div class="flex items-center gap-4">
                <x-ui.checkbox name="is_final" label="Kuis akhir kursus" :checked="old('is_final', $quiz->is_final)" />
                <x-ui.checkbox name="is_published" label="Terbitkan" :checked="old('is_published', $quiz->is_published)" />
            </div>

            <x-ui.button type="submit" class="w-fit">Simpan pengaturan</x-ui.button>
        </section>
    </form>

    {{-- Daftar pertanyaan --}}
    <div class="card">
        <section class="grid gap-4 p-5">
            <h2 class="font-medium">Pertanyaan</h2>

            @forelse ($quiz->questions as $question)
                <div class="grid gap-2 border-b border-border pb-4 last:border-b-0 last:pb-0">
                    <div class="flex items-start gap-2">
                        <span class="bg-muted text-muted-foreground flex size-6 shrink-0 items-center justify-center rounded-md text-xs">{{ $loop->iteration }}</span>
                        <p class="flex-1 text-sm font-medium">{{ $question->text }}</p>
                        <form method="POST" action="{{ route('instructor.questions.destroy', [$course, $quiz, $question]) }}" onsubmit="return confirm('Hapus pertanyaan ini?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="icon-sm" aria-label="Hapus pertanyaan"><x-lucide-trash-2 class="size-4" /></x-ui.button>
                        </form>
                    </div>

                    <ul class="ms-8 grid gap-1 text-sm">
                        @foreach ($question->options as $option)
                            <li class="flex items-center gap-2">
                                @if ($option->is_correct)
                                    <x-lucide-check class="size-3.5 text-success" />
                                @else
                                    <x-lucide-minus class="text-muted-foreground size-3.5" />
                                @endif
                                <span>{{ $option->text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-muted-foreground text-sm">Belum ada pertanyaan. Tambahkan di bawah.</p>
            @endforelse
        </section>
    </div>

    {{-- Tambah pertanyaan --}}
    <form method="POST" action="{{ route('instructor.questions.store', [$course, $quiz]) }}" class="card">
        <section class="grid gap-4 p-5">
            @csrf
            <h2 class="font-medium">Tambah pertanyaan</h2>

            <x-ui.textarea name="text" label="Pertanyaan" rows="2" required />
            <x-ui.textarea name="explanation" label="Penjelasan jawaban (opsional)" rows="2" />
            <x-ui.input name="points" type="number" label="Poin" :value="1" min="1" />

            <div class="grid gap-3">
                <span class="label">Pilihan jawaban</span>
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-2">
                        <input type="radio" name="correct" value="{{ $i }}" @checked($i === 0) aria-label="Tandai jawaban benar">
                        <input type="text" name="options[]" class="input" placeholder="Pilihan {{ chr(65 + $i) }}" @required($i < 2)>
                    </div>
                @endfor
                <p class="text-muted-foreground text-xs">Pilih tombol radio di samping jawaban yang benar. Minimal dua pilihan.</p>
            </div>

            <x-ui.button type="submit" class="w-fit">Tambah pertanyaan</x-ui.button>
        </section>
    </form>
</x-layouts.app>
