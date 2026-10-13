{{--
    Kontainer toolbar filter bersama.
    - Menjaga alignment (items-end) & spacing (gap-3) konsisten di semua halaman.
    - `flex-wrap` + kontrol berlebar `w-full sm:w-*` => stack di mobile, satu baris di sm+.
    - Membungkus slot dengan <form> hanya bila `action` diberikan.
--}}
@props([
    'action' => null,
    'method' => 'GET',
])

@php
    $classes = 'flex flex-wrap items-end gap-3';
@endphp

@if ($action)
    <form
        method="{{ $method }}"
        action="{{ $action }}"
        role="search"
        data-filter-bar
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </form>
@else
    <div data-filter-bar {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </div>
@endif
