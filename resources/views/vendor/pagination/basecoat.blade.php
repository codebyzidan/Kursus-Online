@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex w-full justify-center">
        <ul class="flex flex-row items-center gap-1">
            @if ($paginator->onFirstPage())
                <li>
                    <span class="btn" data-variant="ghost" aria-disabled="true">
                        <x-lucide-chevron-left class="size-4" />
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn" data-variant="ghost">
                        <x-lucide-chevron-left class="size-4" />
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <div class="text-muted-foreground flex size-9 items-center justify-center">
                            <x-lucide-ellipsis class="size-4" />
                        </div>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="btn" data-variant="outline" data-size="icon" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="btn" data-variant="ghost" data-size="icon" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn" data-variant="ghost">
                        <span class="hidden sm:inline">Berikutnya</span>
                        <x-lucide-chevron-right class="size-4" />
                    </a>
                </li>
            @else
                <li>
                    <span class="btn" data-variant="ghost" aria-disabled="true">
                        <span class="hidden sm:inline">Berikutnya</span>
                        <x-lucide-chevron-right class="size-4" />
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
