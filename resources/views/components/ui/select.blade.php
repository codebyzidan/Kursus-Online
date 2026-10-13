@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'hint' => null,
    'required' => false,
    'searchable' => false,
    'id' => null,
])

@php
    $id = $id ?? 'field-'.$name;
    $hasError = $errors->has($name);
    $current = old($name, $selected);
@endphp

<div class="field grid gap-2" data-invalid="{{ $hasError ? 'true' : 'false' }}">
    @if ($label)
        <label class="label" for="{{ $id }}">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        @if ($searchable) data-tom-select data-placeholder="{{ $placeholder ?? 'Cari…' }}" @endif
        {{ $attributes->merge(['class' => 'select']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) $current === (string) $value)>{{ $text }}</option>
        @endforeach

        {{ $slot }}
    </select>

    @if ($hint && ! $hasError)
        <p class="text-muted-foreground text-sm">{{ $hint }}</p>
    @endif

    @error($name)
        <p role="alert" id="{{ $id }}-error" class="text-danger text-sm">{{ $message }}</p>
    @enderror
</div>
