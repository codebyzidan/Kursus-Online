@props(['config' => [], 'label' => 'Grafik', 'height' => 'h-72'])

{{-- `min-w-0` agar canvas chart tidak memperlebar item grid/flex-nya. --}}
<div {{ $attributes->merge(['class' => 'w-full min-w-0 '.$height]) }}>
    <canvas
        data-chart="{{ json_encode($config, JSON_UNESCAPED_UNICODE) }}"
        role="img"
        aria-label="{{ $label }}"
    ></canvas>
</div>
