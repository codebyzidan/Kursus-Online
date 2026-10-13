<div id="catalog-results" class="grid gap-4">
    <div class="text-muted-foreground flex items-center justify-between text-sm">
        <span>{{ $courses->total() }} kursus ditemukan</span>
        @if ($courses->total() > 0)
            <span>Halaman {{ $courses->currentPage() }} dari {{ $courses->lastPage() }}</span>
        @endif
    </div>

    @if ($courses->isEmpty())
        <x-ui.empty-state
            icon="search-x"
            title="Kursus tidak ditemukan"
            description="Coba ubah kata kunci atau filter yang Anda gunakan."
        >
            <x-slot:action>
                <x-ui.button :href="route('courses.index')" variant="outline" size="sm">Reset filter</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                <x-ui.course-card :course="$course" />
            @endforeach
        </div>

        <div class="pt-2">
            {{ $courses->links() }}
        </div>
    @endif
</div>
