@props([
    'name',
    'label' => null,
    'value' => null,
    'hint' => null,
    'required' => false,
    'rows' => 4,
    'id' => null,
])

@php
    $id = $id ?? 'field-'.$name;
    $hasError = $errors->has($name);
@endphp

<div class="field grid gap-2" data-invalid="{{ $hasError ? 'true' : 'false' }}">
    @if ($label)
        <label class="label" for="{{ $id }}">{{ $label }}</label>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->merge(['class' => 'textarea']) }}
    >{{ $value }}</textarea>

    @if ($hint && ! $hasError)
        <p class="text-muted-foreground text-sm">{{ $hint }}</p>
    @endif

    @error($name)
        <p role="alert" id="{{ $id }}-error" class="text-danger text-sm">{{ $message }}</p>
    @enderror
</div>
