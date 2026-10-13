@props([
    'variant' => 'ghost',
    'size' => 'icon-sm',
    'label' => 'Buka menu',
    'side' => 'bottom',
    'align' => 'start',
])

@php
    $id = 'menu-'.Illuminate\Support\Str::random(8);
@endphp

{{--
    Popover Basecoat tetap ada di layout saat tertutup (memakai `invisible`, bukan `display:none`).
    Karena itu `data-side`/`data-align` wajib diisi: menu yang bersandar di tepi kanan viewport
    harus memakai align="end" agar popover tidak meluber ke luar layar dan memicu scroll horizontal.
--}}
<div class="dropdown-menu" id="{{ $id }}">
    <button
        type="button"
        id="{{ $id }}-trigger"
        aria-haspopup="menu"
        aria-controls="{{ $id }}-menu"
        aria-expanded="false"
        aria-label="{{ $label }}"
        class="btn"
        data-variant="{{ $variant }}"
        data-size="{{ $size }}"
    >
        {{ $trigger }}
    </button>

    <div
        data-popover
        aria-hidden="true"
        id="{{ $id }}-popover"
        data-side="{{ $side }}"
        data-align="{{ $align }}"
        class="w-48"
    >
        <div role="menu" id="{{ $id }}-menu" aria-labelledby="{{ $id }}-trigger">
            {{ $slot }}
        </div>
    </div>
</div>
