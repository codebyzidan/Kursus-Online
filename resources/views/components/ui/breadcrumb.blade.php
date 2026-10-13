@props(['items' => []])

@if (count($items))
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol>
            @foreach ($items as $label => $url)
                <li>
                    @if ($url && ! $loop->last)
                        <a href="{{ $url }}">{{ $label }}</a>
                    @else
                        <span aria-current="{{ $loop->last ? 'page' : 'false' }}">{{ $label }}</span>
                    @endif
                </li>
                @unless ($loop->last)
                    <li aria-hidden="true">
                        <x-lucide-chevron-right class="size-3.5" />
                    </li>
                @endunless
            @endforeach
        </ol>
    </nav>
@endif
