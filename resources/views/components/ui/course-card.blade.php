@props(['course'])

@php
    $price = $course->getEffectivePriceAttribute();
    $hasDiscount = $course->sale_price !== null && $course->sale_price < $course->price;
@endphp

<article {{ $attributes->merge(['class' => 'card overflow-hidden']) }}>
    <a href="{{ route('courses.show', $course) }}" class="block">
        <div class="bg-muted flex aspect-video w-full items-center justify-center overflow-hidden">
            @if ($course->thumbnail_url)
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="size-full object-cover">
            @else
                <x-lucide-graduation-cap class="text-muted-foreground size-10" />
            @endif
        </div>
    </a>

    <section class="grid gap-3 p-5">
        <div class="flex items-center gap-2">
            @if ($course->category)
                <x-ui.badge variant="neutral">{{ $course->category->name }}</x-ui.badge>
            @endif

            @if ($course->level)
                <x-ui.badge variant="outline">{{ $course->level->label() }}</x-ui.badge>
            @endif
        </div>

        <h3 class="text-sm leading-snug font-semibold">
            <a href="{{ route('courses.show', $course) }}" class="hover:underline">{{ $course->title }}</a>
        </h3>

        <p class="text-muted-foreground line-clamp-2 text-sm">{{ $course->subtitle }}</p>

        <div class="text-muted-foreground flex items-center gap-2 text-xs">
            <x-ui.avatar :name="$course->instructor?->name ?? 'Instruktur'" size="sm" />
            <span>{{ $course->instructor?->name }}</span>
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center gap-1 text-xs">
                <x-lucide-star class="size-3.5 fill-current text-warning" />
                <span class="font-medium tabular-nums">{{ number_format((float) $course->rating_avg, 1) }}</span>
                <span class="text-muted-foreground">({{ $course->rating_count }})</span>
            </div>

            <div class="text-end">
                @if ($hasDiscount)
                    <div class="text-muted-foreground text-xs line-through tabular-nums">
                        Rp{{ number_format($course->price, 0, ',', '.') }}
                    </div>
                @endif
                <div class="text-sm font-semibold tabular-nums">
                    {{ $price === 0 ? 'Gratis' : 'Rp'.number_format($price, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </section>
</article>
