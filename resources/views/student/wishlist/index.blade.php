<x-layouts.app title="Wishlist">
    <x-ui.page-header
        title="Wishlist"
        description="Kursus yang Anda simpan untuk nanti."
        :items="['Dashboard' => route('dashboard'), 'Wishlist' => null]"
    />

    @if ($wishlists->isEmpty())
        <x-ui.empty-state icon="heart" title="Wishlist kosong" description="Simpan kursus yang menarik untuk dibeli nanti.">
            <x-slot:action>
                <x-ui.button :href="route('courses.index')">Jelajahi katalog</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($wishlists as $wishlist)
                <article class="card">
                    <section class="grid gap-3 p-5">
                        <h2 class="text-sm leading-snug font-semibold">
                            <a href="{{ route('courses.show', $wishlist->course) }}" class="hover:underline">{{ $wishlist->course->title }}</a>
                        </h2>
                        <div class="text-muted-foreground flex items-center gap-2 text-xs">
                            <x-ui.avatar :name="$wishlist->course->instructor?->name ?? 'Instruktur'" size="sm" />
                            <span>{{ $wishlist->course->instructor?->name }}</span>
                        </div>
                        <div class="text-sm font-semibold tabular-nums">
                            {{ $wishlist->course->isFree() ? 'Gratis' : 'Rp'.number_format($wishlist->course->getEffectivePriceAttribute(), 0, ',', '.') }}
                        </div>
                    </section>
                    <footer class="flex items-center gap-2 border-t border-border p-4">
                        <form method="POST" action="{{ route('checkout.store') }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="courses[]" value="{{ $wishlist->course->getKey() }}">
                            <x-ui.button type="submit" size="sm" class="w-full">Beli kursus</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('student.wishlist.destroy', $wishlist->course) }}">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="outline" size="sm" aria-label="Hapus dari wishlist">
                                <x-lucide-trash-2 class="size-4" />
                            </x-ui.button>
                        </form>
                    </footer>
                </article>
            @endforeach
        </div>

        <div>{{ $wishlists->links() }}</div>
    @endif
</x-layouts.app>
