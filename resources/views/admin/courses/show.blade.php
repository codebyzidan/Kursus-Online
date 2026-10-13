<x-layouts.app :title="'Tinjau: '.$course->title">
    <x-ui.page-header
        :title="$course->title"
        :description="$course->instructor?->name"
        :items="['Dashboard admin' => route('admin.dashboard'), 'Review kursus' => route('admin.courses.index'), 'Tinjau' => null]"
    >
        <x-slot:actions>
            <x-ui.badge :variant="match ($course->status) {
                \App\Enums\CourseStatus::Published => 'success',
                \App\Enums\CourseStatus::PendingReview => 'warning',
                \App\Enums\CourseStatus::Rejected => 'danger',
                default => 'neutral',
            }">{{ $course->status->label() }}</x-ui.badge>
            <x-ui.button :href="route('courses.show', $course)" variant="outline" target="_blank">Lihat publik</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="card">
            <section class="grid gap-4 p-5">
                <h2 class="font-medium">Kurikulum</h2>

                @forelse ($course->sections as $section)
                    <div class="grid gap-2 border-b border-border pb-3 last:border-b-0 last:pb-0">
                        <div class="text-sm font-medium">{{ $section->title }}</div>
                        <ul class="text-muted-foreground grid gap-1 text-sm">
                            @foreach ($section->lessons as $lesson)
                                <li class="flex items-center gap-2">
                                    <x-lucide-dot class="size-4" />
                                    <span>{{ $lesson->title }}</span>
                                    @if ($lesson->is_preview)<x-ui.badge variant="success">Pratinjau</x-ui.badge>@endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-muted-foreground text-sm">Kursus ini belum memiliki kurikulum.</p>
                @endforelse
            </section>
        </div>

        <div class="grid gap-4">
            <div class="card">
                <section class="grid gap-3 p-5 text-sm">
                    <div class="flex justify-between"><span class="text-muted-foreground">Harga</span><span class="tabular-nums">{{ $course->isFree() ? 'Gratis' : 'Rp'.number_format($course->getEffectivePriceAttribute(), 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Level</span><span>{{ $course->level?->label() }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Kategori</span><span>{{ $course->category?->name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">Durasi</span><span>{{ $course->duration_minutes }} menit</span></div>
                </section>
            </div>

            <div class="card">
                <section class="grid gap-3 p-5">
                    <h2 class="font-medium">Keputusan</h2>

                    <form method="POST" action="{{ route('admin.courses.approve', $course) }}">
                        @csrf
                        <x-ui.button type="submit" class="w-full">Setujui & terbitkan</x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('admin.courses.reject', $course) }}" class="grid gap-3">
                        @csrf
                        <x-ui.textarea name="reason" label="Alasan penolakan" rows="3" required />
                        <x-ui.button type="submit" variant="danger" class="w-full">Tolak kursus</x-ui.button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
