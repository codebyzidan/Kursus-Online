@php
    $lessons = $course->sections->flatMap->lessons->where('is_published', true)->values();
    $index = $lessons->search(fn ($item) => $item->getKey() === $lesson->getKey());
    $prev = $index !== false && $index > 0 ? $lessons[$index - 1] : null;
    $next = $index !== false && $index < $lessons->count() - 1 ? $lessons[$index + 1] : null;
@endphp

<x-layouts.app :title="$lesson->title">
    <x-ui.breadcrumb :items="[
        'Dashboard' => route('dashboard'),
        'Kursus saya' => route('student.courses.index'),
        $course->title => route('student.learning.show', $course),
        $lesson->title => null,
    ]" />

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <div class="grid gap-4">
            {{-- Konten materi --}}
            <div class="overflow-hidden rounded-xl border border-border bg-card">
                @if ($lesson->isVideo())
                    <div class="bg-muted flex aspect-video items-center justify-center">
                        <div class="grid justify-items-center gap-2 text-center">
                            <x-lucide-monitor-play class="text-muted-foreground size-12" />
                            <p class="text-muted-foreground text-sm">Pemutar video — materi hanya dapat diakses siswa terdaftar.</p>
                        </div>
                    </div>
                @elseif ($lesson->type === \App\Enums\LessonType::Text)
                    <div class="prose-sm max-w-none p-6 text-sm leading-relaxed">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                @else
                    <div class="grid gap-3 p-6">
                        <h2 class="font-medium">Lampiran materi</h2>
                        @forelse ($lesson->getMedia('attachments') as $attachment)
                            <a href="{{ $attachment->getUrl() }}" class="flex items-center gap-2 rounded-md border border-border p-3 text-sm hover:bg-muted">
                                <x-lucide-paperclip class="size-4" /> {{ $attachment->name }}
                            </a>
                        @empty
                            <p class="text-muted-foreground text-sm">Belum ada lampiran untuk materi ini.</p>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- Judul & aksi --}}
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="flex-1 text-lg font-semibold tracking-tight">{{ $lesson->title }}</h1>

                @if (in_array($lesson->getKey(), $completedLessonIds, true))
                    <x-ui.badge variant="success">
                        <x-lucide-check class="size-3.5" /> Selesai
                    </x-ui.badge>
                @else
                    <form method="POST" action="{{ route('student.learning.progress', [$course, $lesson]) }}">
                        @csrf
                        <input type="hidden" name="completed" value="1">
                        <x-ui.button type="submit">
                            <x-lucide-check class="size-4" data-icon="inline-start" /> Tandai selesai
                        </x-ui.button>
                    </form>
                @endif
            </div>

            {{-- Navigasi materi --}}
            <div class="flex items-center justify-between gap-3 border-t border-border pt-4">
                @if ($prev)
                    <x-ui.button :href="route('student.learning.lesson', [$course, $prev])" variant="outline" size="sm">
                        <x-lucide-chevron-left class="size-4" data-icon="inline-start" /> Sebelumnya
                    </x-ui.button>
                @else
                    <span></span>
                @endif

                @if ($next)
                    <x-ui.button :href="route('student.learning.lesson', [$course, $next])" variant="outline" size="sm">
                        Berikutnya <x-lucide-chevron-right class="size-4" data-icon="inline-end" />
                    </x-ui.button>
                @else
                    <span></span>
                @endif
            </div>
        </div>

        {{-- Sidebar kurikulum --}}
        <aside class="lg:sticky lg:top-20 lg:h-fit">
            <div class="card">
                <section class="grid gap-4 p-5">
                    <div class="grid gap-2">
                        <h2 class="truncate font-medium">{{ $course->title }}</h2>
                        <x-ui.progress :value="$enrollment?->progress_percent ?? 0" label="Progres kursus" />
                        <x-ui.button :href="route('student.courses.index')" variant="link" size="sm" class="w-fit">Semua kursus saya</x-ui.button>
                    </div>

                    <div class="grid gap-3 border-t border-border pt-4">
                        @foreach ($course->sections as $section)
                            <div class="grid gap-1">
                                <h3 class="text-muted-foreground text-xs font-medium uppercase">{{ $section->title }}</h3>
                                <ul class="grid gap-0.5">
                                    @foreach ($section->lessons as $item)
                                        @php $isActive = $item->getKey() === $lesson->getKey(); $isDone = in_array($item->getKey(), $completedLessonIds, true); @endphp
                                        <li>
                                            <a
                                                href="{{ route('student.learning.lesson', [$course, $item]) }}"
                                                @class([
                                                    'flex items-center gap-2 rounded-md px-2 py-1.5 text-sm',
                                                    'bg-muted font-medium' => $isActive,
                                                    'hover:bg-muted' => ! $isActive,
                                                ])
                                                @if ($isActive) aria-current="page" @endif
                                            >
                                                @if ($isDone)
                                                    <x-lucide-circle-check class="size-4 shrink-0 text-success" />
                                                @else
                                                    <x-lucide-circle class="text-muted-foreground size-4 shrink-0" />
                                                @endif
                                                <span class="min-w-0 flex-1 truncate">{{ $item->title }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>

                    @if ($course->quizzes->isNotEmpty())
                        <div class="grid gap-2 border-t border-border pt-4">
                            <h3 class="text-muted-foreground text-xs font-medium uppercase">Kuis</h3>
                            @foreach ($course->quizzes as $quiz)
                                <a href="{{ route('student.quizzes.show', [$course, $quiz]) }}" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-muted">
                                    <x-lucide-list-checks class="size-4 shrink-0" />
                                    <span class="min-w-0 flex-1 truncate">{{ $quiz->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </aside>
    </div>
</x-layouts.app>
