<x-layouts.app title="Moderasi ulasan">
    <x-ui.page-header
        title="Moderasi ulasan"
        description="Setujui atau hapus ulasan siswa."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Ulasan' => null]"
    />

    <x-ui.filter-bar :action="route('admin.reviews.index')">
        <x-ui.filter-field>
            <x-ui.select name="status" :options="['approved' => 'Disetujui', 'pending' => 'Menunggu']" :selected="request('status')" placeholder="Semua status" />
        </x-ui.filter-field>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
    </x-ui.filter-bar>

    <div class="grid gap-4">
        @forelse ($reviews as $review)
            <div class="card">
                <section class="grid gap-3 p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.avatar :name="$review->user?->name ?? 'Siswa'" size="sm" />
                        <div>
                            <div class="text-sm font-medium">{{ $review->user?->name }}</div>
                            <div class="text-muted-foreground text-xs">{{ $review->course?->title }}</div>
                        </div>

                        <span class="text-muted-foreground flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <x-lucide-star @class(['size-3', 'fill-current text-warning' => $i <= $review->rating, 'text-muted-foreground' => $i > $review->rating]) />
                            @endfor
                        </span>

                        <x-ui.badge :variant="$review->is_approved ? 'success' : 'warning'" class="ms-auto">
                            {{ $review->is_approved ? 'Disetujui' : 'Menunggu' }}
                        </x-ui.badge>
                    </div>

                    @if ($review->body)
                        <p class="text-muted-foreground text-sm">{{ $review->body }}</p>
                    @endif

                    <div class="flex items-center gap-2 border-t border-border pt-3">
                        @unless ($review->is_approved)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf
                                <x-ui.button type="submit" size="sm">Setujui</x-ui.button>
                            </form>
                        @endunless

                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                        </form>
                    </div>
                </section>
            </div>
        @empty
            <x-ui.empty-state icon="star" title="Tidak ada ulasan" description="Belum ada ulasan yang cocok dengan filter." />
        @endforelse
    </div>

    <div>{{ $reviews->links() }}</div>
</x-layouts.app>
