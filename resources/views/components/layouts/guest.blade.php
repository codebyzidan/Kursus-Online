@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    @include('components.layouts.partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground flex min-h-full flex-col antialiased">
    <x-layouts.partials.public-header />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-layouts.partials.public-footer />

    <x-ui.toaster />
</body>
</html>
