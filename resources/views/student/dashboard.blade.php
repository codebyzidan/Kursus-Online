<x-layouts.app title="Dashboard">
    <x-ui.page-header
        title="Halo, {{ auth()->user()->name }}"
        description="Lanjutkan belajar dan pantau progres Anda."
        :items="['Dashboard' => null]"
    >
        <x-slot:actions>
            <x-ui.button :href="route('courses.index')" variant="outline">
                <x-lucide-compass class="size-4" data-icon="inline-start" /> Jelajahi katalog
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Kursus diikuti" :value="$stats['courses']" icon="book-open" />
        <x-ui.stat-card label="Sedang berjalan" :value="$stats['inProgress']" icon="hourglass" />
        <x-ui.stat-card label="Selesai" :value="$stats['completed']" icon="award" />
        <x-ui.stat-card label="Total menit materi" :value="number_format($stats['minutes'], 0, ',', '.')" icon="clock" />
    </div>

    @if ($continue)
        <div class="card">
            <section class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                <div class="bg-muted flex size-16 shrink-0 items-center justify-center rounded-lg">
                    <x-lucide-play class="text-muted-foreground size-6" />
                </div>

                <div class="grid flex-1 gap-2">
                    <span class="text-muted-foreground text-xs uppercase">Lanjutkan belajar</span>
                    <h2 class="font-medium">{{ $continue->course->title }}</h2>
                    <x-ui.progress :value="$continue->progress_percent" />
                </div>

                <x-ui.button :href="route('student.learning.show', $continue->course)">
                    Lanjutkan <x-lucide-arrow-right class="size-4" data-icon="inline-end" />
                </x-ui.button>
            </section>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card">
                <section class="grid gap-4 p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-medium">Kursus saya</h2>
                        <x-ui.button :href="route('student.courses.index')" variant="link" size="sm">Lihat semua</x-ui.button>
                    </div>

                    @if ($enrollments->isEmpty())
                        <x-ui.empty-state icon="book-open" title="Belum ada kursus" description="Mulai dengan menjelajahi katalog kursus.">
                            <x-slot:action>
                                <x-ui.button :href="route('courses.index')" size="sm">Jelajahi katalog</x-ui.button>
                            </x-slot:action>
                        </x-ui.empty-state>
                    @else
                        <ul class="grid gap-3">
                            @foreach ($enrollments->take(4) as $enrollment)
                                <li class="flex items-center gap-3 border-b border-border pb-3 last:border-b-0 last:pb-0">
                                    <div class="bg-muted flex size-10 shrink-0 items-center justify-center rounded-md">
                                        <x-lucide-book-open class="text-muted-foreground size-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('student.learning.show', $enrollment->course) }}" class="truncate text-sm font-medium hover:underline">
                                            {{ $enrollment->course->title }}
                                        </a>
                                        <x-ui.progress :value="$enrollment->progress_percent" class="mt-1" />
                                    </div>
                                    <x-ui.badge :variant="$enrollment->isCompleted() ? 'success' : 'neutral'">
                                        {{ $enrollment->isCompleted() ? 'Selesai' : $enrollment->progress_percent.'%' }}
                                    </x-ui.badge>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </div>

        <div class="grid gap-6">
            <div class="card">
                <section class="grid gap-3 p-5">
                    <h2 class="font-medium">Kuis tersedia</h2>

                    @forelse ($quizzes as $quiz)
                        <a href="{{ route('student.quizzes.show', [$quiz->course, $quiz]) }}" class="flex items-center gap-2 rounded-md border border-border p-3 text-sm hover:bg-muted">
                            <x-lucide-list-checks class="size-4 shrink-0" />
                            <span class="min-w-0 flex-1 truncate">{{ $quiz->title }}</span>
                            <x-lucide-chevron-right class="text-muted-foreground size-4" />
                        </a>
                    @empty
                        <p class="text-muted-foreground text-sm">Belum ada kuis pada kursus Anda.</p>
                    @endforelse
                </section>
            </div>

            <div class="card">
                <section class="grid gap-3 p-5">
                    <h2 class="font-medium">Rekomendasi untuk Anda</h2>

                    @forelse ($recommended as $course)
                        <a href="{{ route('courses.show', $course) }}" class="text-sm hover:underline">{{ $course->title }}</a>
                    @empty
                        <p class="text-muted-foreground text-sm">Tidak ada rekomendasi saat ini.</p>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
