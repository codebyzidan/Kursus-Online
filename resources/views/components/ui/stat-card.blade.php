@props([
    'label',
    'value',
    'icon' => 'activity',
    'delta' => null,
    'direction' => 'up',
    'deltaLabel' => 'dari periode lalu',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    <section class="grid gap-3 p-5">
        <div class="flex items-center gap-2">
            <span class="flex size-8 items-center justify-center rounded-md bg-muted text-muted-foreground">
                <x-dynamic-component :component="'lucide-'.$icon" class="size-4" />
            </span>
            <span class="text-muted-foreground text-xs font-medium tracking-wide uppercase">{{ $label }}</span>
        </div>

        <div class="text-3xl font-semibold tabular-nums tracking-tight">{{ $value }}</div>

        @if ($delta !== null)
            <div class="flex items-center gap-1.5 text-xs">
                @if ($direction === 'up')
                    <x-lucide-trending-up class="size-3.5 text-success" />
                    <span class="text-success font-medium">{{ $delta }}</span>
                @elseif ($direction === 'down')
                    <x-lucide-trending-down class="size-3.5 text-danger" />
                    <span class="text-danger font-medium">{{ $delta }}</span>
                @else
                    <x-lucide-minus class="size-3.5 text-muted-foreground" />
                    <span class="text-muted-foreground font-medium">{{ $delta }}</span>
                @endif
                <span class="text-muted-foreground">{{ $deltaLabel }}</span>
            </div>
        @endif
    </section>
</div>
