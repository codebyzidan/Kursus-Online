@props(['icon' => 'inbox', 'title' => 'Belum ada data', 'description' => null])

<section {{ $attributes->merge(['class' => 'empty']) }}>
    <header>
        <figure>
            <x-dynamic-component :component="'lucide-'.$icon" class="size-6" />
        </figure>
        <h3>{{ $title }}</h3>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </header>

    @isset($action)
        <footer>
            <div class="flex justify-center gap-2">{{ $action }}</div>
        </footer>
    @endisset
</section>
