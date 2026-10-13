<x-layouts.guest :title="'Beranda'" :description="'Katalog kursus online untuk belajar keterampilan digital dari praktisi.'">
    {{-- Hero --}}
    <section class="border-border border-b">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:py-20">
            <div class="grid gap-6">
                <x-ui.badge variant="neutral" class="w-fit">Platform kursus online</x-ui.badge>

                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    Belajar keterampilan digital langsung dari praktisi
                </h1>

                <p class="text-muted-foreground max-w-xl text-sm sm:text-base">
                    {{ \App\Models\Setting::get('site_tagline', 'Kursus video, materi bacaan, dan kuis dengan progres yang jelas dan sertifikat resmi.') }}
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button :href="route('courses.index')" size="lg">
                        Jelajahi katalog
                        <x-lucide-arrow-right class="size-4" data-icon="inline-end" />
                    </x-ui.button>
                    <x-ui.button :href="route('certificates.verify.form')" variant="outline" size="lg">
                        Verifikasi sertifikat
                    </x-ui.button>
                </div>

                <dl class="text-muted-foreground grid grid-cols-3 gap-4 border-t border-border pt-6 text-sm">
                    <div>
                        <dt>Kursus</dt>
                        <dd class="text-foreground text-lg font-semibold tabular-nums">{{ \App\Models\Course::query()->published()->count() }}</dd>
                    </div>
                    <div>
                        <dt>Instruktur</dt>
                        <dd class="text-foreground text-lg font-semibold tabular-nums">{{ \App\Models\User::query()->whereHas('roles', fn ($query) => $query->where('name', \App\Enums\UserRole::Instructor->value))->count() }}</dd>
                    </div>
                    <div>
                        <dt>Siswa</dt>
                        <dd class="text-foreground text-lg font-semibold tabular-nums">{{ \App\Models\Enrollment::query()->count() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-muted/40 border-border flex items-center justify-center rounded-xl border p-10">
                <x-lucide-monitor-play class="text-muted-foreground size-24" />
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    @if ($categories->isNotEmpty())
        <section class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6">
            <h2 class="mb-6 text-lg font-semibold tracking-tight">Jelajahi kategori</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('courses.index', ['category' => $category->slug]) }}"
                        class="card hover:border-foreground/20 transition-colors"
                    >
                        <section class="flex items-center gap-3 p-4">
                            <span class="bg-muted text-muted-foreground flex size-9 items-center justify-center rounded-md">
                                <x-lucide-folder class="size-4" />
                            </span>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium">{{ $category->name }}</div>
                                <div class="text-muted-foreground text-xs">{{ $category->published_courses_count }} kursus</div>
                            </div>
                        </section>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Kursus terpopuler --}}
    @if ($featured->isNotEmpty())
        <section class="mx-auto w-full max-w-7xl px-4 pb-14 sm:px-6">
            <div class="mb-6 flex items-end justify-between gap-4">
                <h2 class="text-lg font-semibold tracking-tight">Kursus terpopuler</h2>
                <x-ui.button :href="route('courses.index', ['sort' => 'popular'])" variant="link" size="sm">
                    Lihat semua
                </x-ui.button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $course)
                    <x-ui.course-card :course="$course" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Terbaru --}}
    @if ($latest->isNotEmpty())
        <section class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6">
            <h2 class="mb-6 text-lg font-semibold tracking-tight">Baru ditambahkan</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($latest as $course)
                    <x-ui.course-card :course="$course" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.guest>
