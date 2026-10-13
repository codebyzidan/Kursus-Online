@props(['src' => null, 'name' => '', 'size' => null])

@php
    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');

    $base = ['class' => 'avatar'];

    if ($size && $size !== 'default') {
        $base['data-size'] = $size;
    }
@endphp

<span {{ $attributes->merge($base) }} title="{{ $name }}">
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}">
    @endif
    <span>{{ $initials ?: '?' }}</span>
</span>
