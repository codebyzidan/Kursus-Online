@php
    $price = $course->getEffectivePriceAttribute();
    $lessons = $course->sections->flatMap->lessons;
    $wishlisted = auth()->check() && \App\Models\Wishlist::query()
        ->where('user_id', auth()->id())
        ->where('course_id', $course->getKey())
        ->exists();
@endphp

<x-layouts.guest :title="$course->title" :description="$course->meta_description ?? $course->subtitle">
    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6">
        <x-ui.breadcrumb :items="[
            'Beranda' => route('home'),
            'Katalog' => route('courses.index'),
            $course->title => null,
        ]" />

        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="grid gap-8">
                {{-- Header kursus --}}
                <header class="grid gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($course->category)
                            <x-ui.badge variant="neutral">{{ $course->category->name }}</x-ui.badge>
                        @endif
                        <x-ui.badge variant="outline">{{ $course->level->label() }}</x-ui.badge>
                    </div>

                    <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">{{ $course->title }}</h1>

                    @if ($course->subtitle)
                        <p class="text-muted-foreground">{{ $course->subtitle }}</p>
                    @endif

                    <div class="text-muted-foreground flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                        <a href="{{ route('instructors.show', $course->instructor) }}" class="hover:text-foreground flex items-center gap-2">
                            <x-ui.avatar :src="$course->instructor?->avatar_url" :name="$course->instructor?->name ?? 'Instruktur'" size="sm" />
                            <span>{{ $course->instructor?->name }}</span>
                        </a>
                        <span class="flex items-center gap-1">
                            <x-lucide-star class="size-3.5 fill-current text-warning" />
                            <span class="tabular-nums">{{ number_format((float) $course->rating_avg, 1) }}</span>
                            <span>({{ $course->rating_count }} ulasan)</span>
                        </span>
                        <span class="flex items-center gap-1"><x-lucide-users class="size-3.5" /> {{ $course->students_count }} siswa</span>
                        <span class="flex items-center gap-1"><x-lucide-clock class="size-3.5" /> {{ $course->duration_minutes }} menit</span>
                    </div>
                </header>

                @if ($course->description)
                    <section class="card">
                        <section class="grid gap-3 p-5">
                            <h2 class="font-medium">Tentang kursus ini</h2>
                            <div class="text-muted-foreground space-y-3 text-sm leading-relaxed">
                                {!! nl2br(e($course->description)) !!}
                            </div>
                        </section>
                    </section>
                @endif

                @if (! empty($course->outcomes))
                    <section class="card">
                        <section class="grid gap-3 p-5">
                            <h2 class="font-medium">Yang akan Anda pelajari</h2>
                            <ul class="grid gap-2 sm:grid-cols-2">
                                @foreach ($course->outcomes as $outcome)
                                    <li class="flex items-start gap-2 text-sm">
                                        <x-lucide-check class="mt-0.5 size-4 shrink-0 text-success" />
                                        <span>{{ $outcome }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    </section>
                @endif

                {{-- Kurikulum --}}
                <section class="card">
                    <section class="grid gap-3 p-5">
                        <div class="flex items-center justify-between">
                            <h2 class="font-medium">Kurikulum kursus</h2>
                            <span class="text-muted-foreground text-xs">{{ $course->sections->count() }} bagian &middot; {{ $lessons->count() }} materi</span>
                        </div>

                        <div class="accordion">
                            @foreach ($course->sections as $section)
                                <details @if ($loop->first) open @endif>
                                    <summary>{{ $section->title }}</summary>
                                    <ul class="grid gap-1 text-sm">
                                        @foreach ($section->lessons as $lesson)
                                            <li class="text-muted-foreground flex items-center gap-2 py-1">
                                                @switch($lesson->type)
                                                    @case(\App\Enums\LessonType::Video)
                                                        <x-lucide-circle-play class="size-4 shrink-0" />
                                                        @break
                                                    @case(\App\Enums\LessonType::Text)
                                                        <x-lucide-file-text class="size-4 shrink-0" />
                                                        @break
                                                    @default
                                                        <x-lucide-paperclip class="size-4 shrink-0" />
                                                @endswitch

                                                <span class="text-foreground">{{ $lesson->title }}</span>

                                                @if ($lesson->is_preview)
                                                    <x-ui.badge variant="success" class="ms-auto">Pratinjau</x-ui.badge>
                                                @endif

                                                <span class="ms-auto tabular-nums">{{ gmdate('i:s', $lesson->duration) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endforeach
                        </div>
                    </section>
                </section>

                {{-- Ulasan --}}
                <section class="card">
                    <section class="grid gap-4 p-5">
                        <h2 class="font-medium">Ulasan siswa</h2>

                        @if ($reviewable)
                            <form method="POST" action="{{ route('student.reviews.store', $course) }}" class="grid gap-3 rounded-lg border border-border p-4">
                                @csrf
                                <x-ui.select name="rating" label="Rating" :options="[5 => '5 - Sangat baik', 4 => '4 - Baik', 3 => '3 - Cukup', 2 => '2 - Kurang', 1 => '1 - Buruk']" :selected="5" required />
                                <x-ui.textarea name="body" label="Ulasan Anda" rows="3" />
                                <x-ui.button type="submit" class="w-fit">Kirim ulasan</x-ui.button>
                            </form>
                        @endif

                        @forelse ($course->approvedReviews as $review)
                            <article class="grid gap-2 border-t border-border pt-4 first:border-t-0 first:pt-0">
                                <div class="flex items-center gap-2">
                                    <x-ui.avatar :name="$review->user?->name ?? 'Siswa'" size="sm" />
                                    <div>
                                        <div class="text-sm font-medium">{{ $review->user?->name }}</div>
                                        <div class="text-muted-foreground flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <x-lucide-star @class(['size-3', 'fill-current text-warning' => $i <= $review->rating, 'text-muted-foreground' => $i > $review->rating]) />
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-muted-foreground ms-auto text-xs">{{ $review->created_at?->translatedFormat('d M Y') }}</span>
                                </div>

                                @if ($review->body)
                                    <p class="text-muted-foreground text-sm">{{ $review->body }}</p>
                                @endif

                                @if ($review->instructor_reply)
                                    <div class="bg-muted/50 rounded-md p-3 text-sm">
                                        <div class="mb-1 font-medium">Balasan instruktur</div>
                                        <p class="text-muted-foreground">{{ $review->instructor_reply }}</p>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="text-muted-foreground text-sm">Belum ada ulasan yang disetujui.</p>
                        @endforelse
                    </section>
                </section>
            </div>

            {{-- Kartu pembelian (sticky) --}}
            <aside class="lg:sticky lg:top-20 lg:h-fit">
                <div class="card overflow-hidden">
                    <div class="bg-muted flex aspect-video items-center justify-center">
                        @if ($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="size-full object-cover">
                        @else
                            <x-lucide-graduation-cap class="text-muted-foreground size-10" />
                        @endif
                    </div>

                    <section class="grid gap-4 p-5">
                        <div>
                            @if ($course->sale_price !== null && $course->sale_price < $course->price)
                                <div class="text-muted-foreground text-sm line-through tabular-nums">
                                    Rp{{ number_format($course->price, 0, ',', '.') }}
                                </div>
                            @endif
                            <div class="text-2xl font-semibold tabular-nums tracking-tight">
                                {{ $price === 0 ? 'Gratis' : 'Rp'.number_format($price, 0, ',', '.') }}
                            </div>
                        </div>

                        @auth
                            @if ($enrollment)
                                <x-ui.button :href="route('student.learning.show', $course)" size="lg" class="w-full">
                                    Lanjutkan belajar
                                </x-ui.button>

                                @if ($enrollment->progress_percent > 0)
                                    <x-ui.progress :value="$enrollment->progress_percent" label="Progres" />
                                @endif
                            @else
                                <form method="POST" action="{{ route('checkout.store') }}">
                                    @csrf
                                    <input type="hidden" name="courses[]" value="{{ $course->getKey() }}">
                                    <x-ui.button type="submit" size="lg" class="w-full">
                                        {{ $price === 0 ? 'Ikuti kursus gratis' : 'Beli kursus' }}
                                    </x-ui.button>
                                </form>
                            @endif

                            <div class="flex items-center gap-2">
                                @if ($wishlisted)
                                    <form method="POST" action="{{ route('student.wishlist.destroy', $course) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="outline" class="w-full">Hapus dari wishlist</x-ui.button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('student.wishlist.store', $course) }}">
                                        @csrf
                                        <x-ui.button type="submit" variant="outline" class="w-full">
                                            <x-lucide-heart class="size-4" data-icon="inline-start" /> Wishlist
                                        </x-ui.button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <x-ui.button :href="route('login')" size="lg" class="w-full">Masuk untuk membeli</x-ui.button>
                            <p class="text-muted-foreground text-center text-xs">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-foreground underline underline-offset-4">Daftar di sini</a>
                            </p>
                        @endauth

                        <ul class="text-muted-foreground grid gap-2 border-t border-border pt-4 text-sm">
                            <li class="flex items-center gap-2"><x-lucide-infinity class="size-4" /> Akses selamanya</li>
                            <li class="flex items-center gap-2"><x-lucide-award class="size-4" /> Sertifikat penyelesaian</li>
                            <li class="flex items-center gap-2"><x-lucide-smartphone class="size-4" /> Belajar dari mobile</li>
                        </ul>
                    </section>
                </div>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-12">
                <h2 class="mb-6 text-lg font-semibold tracking-tight">Kursus terkait</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-ui.course-card :course="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.guest>
