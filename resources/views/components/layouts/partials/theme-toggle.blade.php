{{-- Tombol ganti mode gelap/terang memakai API Basecoat (persist via localStorage). --}}
<button
    type="button"
    class="btn"
    data-variant="ghost"
    data-size="icon-sm"
    aria-label="Ganti mode gelap"
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    x-on:click="window.basecoat.theme.toggle(); dark = document.documentElement.classList.contains('dark')"
    x-on:basecoat:themechange.window="dark = document.documentElement.classList.contains('dark')"
>
    <x-lucide-sun class="size-4" x-show="dark" x-cloak />
    <x-lucide-moon class="size-4" x-show="!dark" x-cloak />
</button>
