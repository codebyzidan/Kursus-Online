@props(['title' => null, 'heading' => null, 'description' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>

    @include('components.layouts.partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground flex min-h-screen items-center justify-center p-4 antialiased">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="mb-6 flex items-center justify-center gap-2 font-semibold">
            <x-lucide-graduation-cap class="size-5" />
            <span>{{ config('app.name') }}</span>
        </a>

        <div class="card">
            <section class="grid gap-5 p-6">
                @if ($heading)
                    <header class="grid gap-1">
                        <h1 class="text-lg font-semibold tracking-tight">{{ $heading }}</h1>
                        @if ($description)
                            <p class="text-muted-foreground text-sm">{{ $description }}</p>
                        @endif
                    </header>
                @endif

                {{ $slot }}
            </section>
        </div>

        @isset($footer)
            <div class="text-muted-foreground mt-4 text-center text-sm">{{ $footer }}</div>
        @endisset
    </div>

    <x-ui.toaster />
</body>
</html>
