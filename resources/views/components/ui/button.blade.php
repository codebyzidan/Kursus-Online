@props([
    'variant' => 'primary',
    'size' => null,
    'href' => null,
    'type' => 'button',
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

    $dataVariant = $variantMap[$variant] ?? $variant;

    $base = ['class' => 'btn'];

    if ($dataVariant) {
        $base['data-variant'] = $dataVariant;
    }

    if ($size) {
        $base['data-size'] = $size;
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge($base) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge($base) }}>
        {{ $slot }}
    </button>
@endif
