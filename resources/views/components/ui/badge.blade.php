@props([
    'variant' => 'primary',
    'icon' => false,
])

@php
    $variantMap = [
        'primary' => 'primary',
        'secondary' => 'secondary',
        'outline' => 'outline',
        'ghost' => 'ghost',
        'link' => 'link',
        'danger' => 'destructive',
        'destructive' => 'destructive',
    ];

    // Status memakai token *-soft agar kontras tetap AA di terang & gelap.
    $soft = [
        'success' => 'bg-success-soft text-success border border-success/30',
        'warning' => 'bg-warning-soft text-warning border border-warning/30',
        'danger' => 'bg-danger-soft text-danger border border-danger/30',
        'info' => 'bg-muted text-foreground border border-border',
        'neutral' => 'bg-muted text-muted-foreground border border-border',
    ];

    $softClass = $soft[$variant] ?? null;
    $dataVariant = $variantMap[$variant] ?? null;
@endphp

@if ($softClass)
    <span {{ $attributes->merge(['class' => 'badge '.$softClass]) }}>
        {{ $slot }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'badge', 'data-variant' => $dataVariant ?? 'secondary']) }}>
        {{ $slot }}
    </span>
@endif
