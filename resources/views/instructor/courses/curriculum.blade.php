<x-layouts.app :title="'Kurikulum: '.$course->title">
    <x-ui.page-header
        title="Kurikulum"
        :description="$course->title"
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kelola kursus' => route('instructor.courses.index'), 'Kurikulum' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('instructor.courses.edit', $course)" variant="outline">Informasi kursus</x-ui.button>
            <x-ui.button :href="route('courses.show', $course)" variant="outline" target="_blank">Pratinjau</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @error('curriculum')
        <x-ui.alert variant="destructive" title="Kurikulum belum lengkap">{{ $message }}</x-ui.alert>
    @enderror

    {{-- Tambah bagian --}}
    <div class="card">
        <form method="POST" action="{{ route('instructor.sections.store', $course) }}">
            @csrf
            <section class="flex flex-col gap-3 p-5 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-ui.input name="title" label="Judul bagian baru" placeholder="Contoh: Pengenalan" required />
                </div>
                <x-ui.button type="submit">
                    <x-lucide-plus class="size-4" data-icon="inline-start" /> Tambah bagian
                </x-ui.button>
            </section>
        </form>
    </div>

    {{-- Daftar bagian (drag untuk mengurutkan) --}}
    <div
        class="grid gap-4"
        data-sortable
        data-sortable-url="{{ route('instructor.sections.reorder', $course) }}"
        data-sortable-handle="[data-drag-handle]"
    >
        @forelse ($course->sections as $section)
            <div class="card" data-sortable-id="{{ $section->getKey() }}">
                <header class="flex items-center gap-2">
                    <button type="button" data-drag-handle class="btn" data-variant="ghost" data-size="icon-sm" aria-label="Geser bagian">
                        <x-lucide-grip-vertical class="size-4" />
                    </button>
                    <h2 class="text-sm font-medium">{{ $section->title }}</h2>
                    <span class="text-muted-foreground text-xs">{{ $section->lessons->count() }} materi</span>

                    <div class="ms-auto flex items-center gap-1">
                        <form method="POST" action="{{ route('instructor.sections.destroy', [$course, $section]) }}" onsubmit="return confirm('Hapus bagian ini beserta materinya?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="icon-sm" aria-label="Hapus bagian">
                                <x-lucide-trash-2 class="size-4" />
                            </x-ui.button>
                        </form>
                    </div>
                </header>

                <section class="grid gap-3 p-5">
                    <ul
                        class="grid gap-2"
                        data-sortable
                        data-sortable-url="{{ route('instructor.lessons.reorder', [$course, $section]) }}"
                        data-sortable-handle="[data-drag-handle]"
                    >
                        @foreach ($section->lessons as $lesson)
                            <li class="rounded-md border border-border" data-sortable-id="{{ $lesson->getKey() }}">
                                <div class="flex items-center gap-2 p-3">
                                    <button type="button" data-drag-handle class="btn" data-variant="ghost" data-size="icon-sm" aria-label="Geser materi">
                                        <x-lucide-grip-vertical class="size-4" />
                                    </button>

                                    @switch($lesson->type)
                                        @case(\App\Enums\LessonType::Video)
                                            <x-lucide-circle-play class="text-muted-foreground size-4" />
                                            @break
                                        @case(\App\Enums\LessonType::Text)
                                            <x-lucide-file-text class="text-muted-foreground size-4" />
                                            @break
                                        @default
                                            <x-lucide-paperclip class="text-muted-foreground size-4" />
                                    @endswitch

                                    <span class="min-w-0 flex-1 truncate text-sm">{{ $lesson->title }}</span>

                                    @if ($lesson->is_preview)
                                        <x-ui.badge variant="success">Pratinjau</x-ui.badge>
                                    @endif

                                    <details class="relative">
                                        <summary class="btn" data-variant="ghost" data-size="sm">Edit</summary>
                                        <div class="bg-card border-border z-10 mt-2 grid gap-3 rounded-md border p-4">
                                            <form method="POST" action="{{ route('instructor.lessons.update', [$course, $section, $lesson]) }}" class="grid gap-3">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.input name="title" label="Judul materi" :value="$lesson->title" required />
                                                <x-ui.select name="type" label="Tipe" :options="collect(\App\Enums\LessonType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])" :selected="$lesson->type->value" required />
                                                <x-ui.input name="video_url" type="url" label="URL video" :value="$lesson->video_url" />
                                                <x-ui.textarea name="content" label="Konten teks" rows="3">{{ $lesson->content }}</x-ui.textarea>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <x-ui.input name="duration" type="number" label="Durasi (detik)" :value="$lesson->duration" min="0" />
                                                    <x-ui.checkbox name="is_preview" label="Pratinjau gratis" :checked="$lesson->is_preview" />
                                                </div>
                                                <x-ui.button type="submit" size="sm" class="w-fit">Simpan materi</x-ui.button>
                                            </form>

                                            <form method="POST" action="{{ route('instructor.lessons.destroy', [$course, $section, $lesson]) }}" onsubmit="return confirm('Hapus materi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="danger" size="sm">Hapus materi</x-ui.button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <details>
                        <summary class="btn" data-variant="outline" data-size="sm">
                            <x-lucide-plus class="size-4" data-icon="inline-start" /> Tambah materi
                        </summary>
                        <form method="POST" action="{{ route('instructor.lessons.store', [$course, $section]) }}" class="mt-3 grid gap-3 rounded-md border border-border p-4">
                            @csrf
                            <x-ui.input name="title" label="Judul materi" required />
                            <x-ui.select name="type" label="Tipe materi" :options="collect(\App\Enums\LessonType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])" :selected="'video'" required />
                            <x-ui.input name="video_url" type="url" label="URL video (untuk tipe video)" />
                            <x-ui.textarea name="content" label="Konten (untuk tipe teks)" rows="3" />
                            <div class="grid grid-cols-2 gap-3">
                                <x-ui.input name="duration" type="number" label="Durasi (detik)" min="0" />
                                <x-ui.checkbox name="is_preview" label="Pratinjau gratis" />
                            </div>
                            <x-ui.button type="submit" size="sm" class="w-fit">Tambahkan</x-ui.button>
                        </form>
                    </details>
                </section>
            </div>
        @empty
            <x-ui.empty-state icon="list-tree" title="Belum ada bagian" description="Tambahkan bagian pertama untuk menyusun kurikulum." />
        @endforelse
    </div>
</x-layouts.app>
