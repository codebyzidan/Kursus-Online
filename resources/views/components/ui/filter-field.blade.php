{{--
    Pembungkus satu kontrol filter dengan lebar konsisten.
    Mobile: selebar penuh (menumpuk). sm+: lebar tetap sesuai `width`.
--}}
@props([
    'width' => 'sm:w-52',
])

<div {{ $attributes->merge(['class' => 'w-full '.$width]) }}>
    {{ $slot }}
</div>
