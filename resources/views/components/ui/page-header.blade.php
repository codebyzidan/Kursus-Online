@props(['items' => [], 'title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="grid gap-1.5">
        @if (count($items))
            <x-ui.breadcrumb :items="$items" />
        @endif

        @if ($title)
            <h1 class="text-xl font-semibold tracking-tight">{{ $title }}</h1>
        @endif

        @if ($description)
            <p class="text-muted-foreground text-sm">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
