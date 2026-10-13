@props(['name' => 'dark_mode', 'label' => 'Mode gelap', 'checked' => false])

@php
    $id = 'switch-'.str_replace(['[', ']'], '-', $name);
    $isChecked = (bool) old($name, $checked);
@endphp

<div class="flex items-center justify-between gap-3 rounded-lg border border-border px-4 py-3">
    <label for="{{ $id }}" class="label leading-normal">{{ $label }}</label>
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" role="switch" @checked($isChecked) {{ $attributes }}>
</div>
