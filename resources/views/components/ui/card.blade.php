@props(['title' => null, 'description' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || isset($header) || isset($action))
        <header>
            @if ($title)
                <h2>{{ $title }}</h2>
            @endif
            @if ($description)
                <p>{{ $description }}</p>
            @endif
            @isset($action)
                <div class="card-action">{{ $action }}</div>
            @endisset
        </header>
    @endif

    <section @class(['px-5 py-5' => $padding])>
        {{ $slot }}
    </section>

    @isset($footer)
        <footer>{{ $footer }}</footer>
    @endisset
</div>
