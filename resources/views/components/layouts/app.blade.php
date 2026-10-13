@props(['title' => null])

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
<body class="bg-background text-foreground min-h-full antialiased">
    <x-layouts.partials.sidebar />

    {{-- `min-w-0` mencegah konten lebar (tabel/chart) memperlebar kolom ini. --}}
    <div class="flex min-h-screen min-w-0 flex-col">
        <x-layouts.partials.topbar :title="$title" />

        <main class="min-w-0 flex-1 px-4 py-6 sm:px-6">
            <div class="app-content mx-auto grid w-full max-w-7xl min-w-0 gap-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    <x-ui.toaster />
</body>
</html>
