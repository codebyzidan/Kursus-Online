@props(['id' => null, 'title' => null, 'description' => null, 'size' => null])

@php
    $id = $id ?? 'dialog-'.Illuminate\Support\Str::random(8);
@endphp

<dialog
    id="{{ $id }}"
    class="dialog"
    @if ($title) aria-labelledby="{{ $id }}-title" @endif
    @if ($description) aria-describedby="{{ $id }}-description" @endif
>
    <div @class(['w-full' => true, 'max-w-lg' => ! $size])>
        @if ($title || $description)
            <header>
                @if ($title)
                    <h2 id="{{ $id }}-title">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p id="{{ $id }}-description">{{ $description }}</p>
                @endif
            </header>
        @endif

        <section>{{ $slot }}</section>

        @isset($footer)
            <footer>{{ $footer }}</footer>
        @endisset
    </div>
</dialog>
