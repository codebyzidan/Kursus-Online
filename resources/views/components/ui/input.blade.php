@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
    'id' => null,
    'bag' => 'default',
])

@php
    $id = $id ?? 'field-'.str_replace(['[', ']'], '-', $name);
    $errorBag = $errors->{$bag};
    $hasError = $errorBag->has($name);
@endphp

<div class="field grid gap-2" data-invalid="{{ $hasError ? 'true' : 'false' }}">
    @if ($label)
        <label class="label" for="{{ $id }}">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($value !== null) value="{{ $value }}" @endif
        @if ($required) required @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->merge(['class' => 'input']) }}
    >

    @if ($hint && ! $hasError)
        <p class="text-muted-foreground text-sm">{{ $hint }}</p>
    @endif

    @error($name, $bag)
        <p role="alert" id="{{ $id }}-error" class="text-danger text-sm">{{ $message }}</p>
    @enderror
</div>
