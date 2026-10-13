@props(['variant' => 'default', 'title' => null])

@php
    $icons = [
        'default' => 'info',
        'info' => 'info',
        'success' => 'circle-check',
        'warning' => 'triangle-alert',
        'destructive' => 'circle-x',
        'danger' => 'circle-x',
    ];

    $dataVariant = in_array($variant, ['destructive', 'default'], true) ? $variant : null;
    $color = match ($variant) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'destructive', 'danger' => 'text-danger',
        default => 'text-muted-foreground',
    };
@endphp

<div {{ $attributes->merge(['class' => 'alert rounded-lg border border-border bg-card p-4']) }} @if ($dataVariant && $dataVariant !== 'default') data-variant="{{ $dataVariant }}" @endif>
    <x-dynamic-component :component="'lucide-'.$icons[$variant]" class="size-4 shrink-0 {{ $color }}" />

    @if ($title)
        <h2 class="font-medium">{{ $title }}</h2>
    @endif

    <section class="text-sm">{{ $slot }}</section>

    @isset($footer)
        <footer class="ms-auto">{{ $footer }}</footer>
    @endisset
</div>
