{{--
    Input pencarian dengan ikon. Sengaja TIDAK merender <form> sendiri agar aman dipakai
    di dalam form toolbar (mencegah form bersarang yang merusak layout).
    Untuk pencarian mandiri, bungkus dengan: <x-ui.filter-bar :action="route(...)"> ... </x-ui.filter-bar>
--}}
@props([
    'query' => null,
    'placeholder' => 'Cari…',
    'name' => 'q',
])

<div {{ $attributes->merge(['class' => 'relative w-full sm:w-72']) }} data-search-input>
    <x-lucide-search class="text-muted-foreground pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2" />
    <input
        type="search"
        name="{{ $name }}"
        value="{{ $query }}"
        placeholder="{{ $placeholder }}"
        aria-label="{{ $placeholder }}"
        class="input ps-9"
    >
</div>
