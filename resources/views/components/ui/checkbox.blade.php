@props(['name', 'label' => null, 'checked' => false, 'hint' => null])

@php
    $id = 'field-'.$name;
    $isChecked = (bool) old($name, $checked);
@endphp

<div class="flex items-start gap-3">
    <input
        type="checkbox"
        id="{{ $id }}"
        name="{{ $name }}"
        value="1"
        @checked($isChecked)
        {{ $attributes }}
    >

    @if ($label)
        <div class="grid gap-0.5">
            <label class="label leading-normal" for="{{ $id }}">{{ $label }}</label>
            @if ($hint)
                <p class="text-muted-foreground text-sm">{{ $hint }}</p>
            @endif
        </div>
    @endif
</div>
