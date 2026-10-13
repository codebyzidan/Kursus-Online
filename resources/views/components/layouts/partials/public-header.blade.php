@php
    $user = auth()->user();
@endphp

<header class="border-border bg-card/80 sticky top-0 z-30 border-b backdrop-blur-none">
    <div class="mx-auto flex h-14 w-full max-w-7xl items-center gap-4 px-4 sm:px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
            <x-lucide-graduation-cap class="size-4" />
            <span>{{ config('app.name') }}</span>
        </a>

        <nav class="text-muted-foreground ms-4 hidden items-center gap-1 text-sm md:flex" aria-label="Navigasi utama">
            <a href="{{ route('courses.index') }}" class="hover:text-foreground rounded-md px-2.5 py-1.5 @if (request()->routeIs('courses.*')) text-foreground font-medium @endif">
                Katalog
            </a>
            <a href="{{ route('certificates.verify.form') }}" class="hover:text-foreground rounded-md px-2.5 py-1.5 @if (request()->routeIs('certificates.verify*')) text-foreground font-medium @endif">
                Verifikasi sertifikat
            </a>
        </nav>

        <div class="ms-auto flex items-center gap-2">
            <x-layouts.partials.theme-toggle />

            @auth
                <x-ui.button :href="route('dashboard')" size="sm">Dashboard</x-ui.button>
            @else
                <x-ui.button :href="route('login')" variant="outline" size="sm">Masuk</x-ui.button>
                <x-ui.button :href="route('register')" size="sm">Daftar</x-ui.button>
            @endauth
        </div>
    </div>
</header>
