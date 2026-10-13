<x-layouts.guest :title="$instructor->name" :description="$instructor->headline">
    <div class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6">
        <x-ui.breadcrumb :items="['Beranda' => route('home'), 'Katalog' => route('courses.index'), $instructor->name => null]" />

        <header class="card mt-6">
            <section class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center">
                <x-ui.avatar :src="$instructor->avatar_url" :name="$instructor->name" size="lg" />

                <div class="grid gap-1.5">
                    <h1 class="text-xl font-semibold tracking-tight">{{ $instructor->name }}</h1>
                    @if ($instructor->headline)
                        <p class="text-muted-foreground text-sm">{{ $instructor->headline }}</p>
                    @endif
                    @if ($instructor->bio)
                        <p class="text-muted-foreground max-w-2xl text-sm">{{ $instructor->bio }}</p>
                    @endif
                </div>

                <dl class="ms-auto grid grid-cols-3 gap-6 text-center">
                    <div>
                        <dt class="text-muted-foreground text-xs">Kursus</dt>
                        <dd class="text-lg font-semibold tabular-nums">{{ $stats['courses'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground text-xs">Siswa</dt>
                        <dd class="text-lg font-semibold tabular-nums">{{ $stats['students'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground text-xs">Rating</dt>
                        <dd class="text-lg font-semibold tabular-nums">{{ $stats['rating'] ?: '—' }}</dd>
                    </div>
                </dl>
            </section>
        </header>

        <section class="mt-8">
            <h2 class="mb-4 text-lg font-semibold tracking-tight">Kursus oleh {{ $instructor->name }}</h2>

            @if ($courses->isEmpty())
                <x-ui.empty-state icon="book-open" title="Belum ada kursus" description="Instruktur ini belum menerbitkan kursus." />
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        <x-ui.course-card :course="$course" />
                    @endforeach
                </div>

                <div class="pt-6">{{ $courses->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts.guest>
