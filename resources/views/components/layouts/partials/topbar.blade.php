@props(['title' => null])

<div class="border-border bg-card/80 sticky top-0 z-20 flex h-14 items-center gap-3 border-b px-4 sm:px-6">
    <button
        type="button"
        class="btn md:hidden"
        data-variant="outline"
        data-size="icon-sm"
        aria-label="Buka menu"
        onclick="document.getElementById('sidebar')?.toggle()"
    >
        <x-lucide-menu class="size-4" />
    </button>

    @if ($title)
        <span class="truncate text-sm font-medium">{{ $title }}</span>
    @endif

    <div class="ms-auto flex items-center gap-2">
        <x-layouts.partials.theme-toggle />

        {{-- align="end" agar popover tidak meluber ke tepi kanan viewport. --}}
        <x-ui.dropdown label="Menu pengguna" align="end">
            <x-slot:trigger>
                <x-ui.avatar :src="auth()->user()->avatar_url" :name="auth()->user()->name" size="sm" />
            </x-slot:trigger>

            <div class="px-2 py-1.5 text-sm">
                <div class="truncate font-medium">{{ auth()->user()->name }}</div>
                <div class="text-muted-foreground truncate text-xs">{{ auth()->user()->email }}</div>
            </div>

            <hr role="separator">

            <div role="menuitem">
                <a href="{{ route('profile.edit') }}" class="block">Profil</a>
            </div>
            <div role="menuitem">
                <a href="{{ route('home') }}" class="block">Lihat situs</a>
            </div>

            <hr role="separator">

            <div role="menuitem">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-start">Keluar</button>
                </form>
            </div>
        </x-ui.dropdown>
    </div>
</div>
