<x-layouts.guest :title="'Katalog kursus'" :description="'Cari kursus berdasarkan kategori, level, harga, dan rating.'">
    <div class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6">
        <x-ui.page-header
            title="Katalog kursus"
            description="Temukan kursus yang sesuai dengan tujuan belajar Anda."
            :items="['Beranda' => route('home'), 'Katalog' => null]"
        />

        <div class="mt-6 grid gap-6 lg:grid-cols-[260px_1fr]">
            {{-- Filter --}}
            <form
                id="catalog-filters"
                method="GET"
                action="{{ route('courses.index') }}"
                class="card h-fit"
                hx-get="{{ route('courses.index') }}"
                hx-target="#catalog-results"
                hx-swap="outerHTML"
                hx-trigger="change, submit"
                hx-push-url="true"
            >
                <section class="grid gap-5 p-5">
                    <div class="grid gap-2">
                        <label class="label" for="filter-q">Kata kunci</label>
                        <input type="search" id="filter-q" name="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="Judul atau deskripsi" x-on:keyup.debounce.400ms="$el.form.requestSubmit()">
                    </div>

                    <div class="grid gap-2">
                        <label class="label" for="filter-category">Kategori</label>
                        <select id="filter-category" name="category" class="select">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected(($filters['category'] ?? null) === $category->slug)>{{ $category->name }}</option>
                                @foreach ($category->children as $child)
                                    <option value="{{ $child->slug }}" @selected(($filters['category'] ?? null) === $child->slug)>&mdash; {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <label class="label" for="filter-level">Level</label>
                        <select id="filter-level" name="level" class="select">
                            <option value="">Semua level</option>
                            @foreach (\App\Enums\CourseLevel::cases() as $level)
                                <option value="{{ $level->value }}" @selected(($filters['level'] ?? null) === $level->value)>{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <label class="label" for="filter-price">Harga</label>
                        <select id="filter-price" name="price" class="select">
                            <option value="">Semua harga</option>
                            <option value="free" @selected(($filters['price'] ?? null) === 'free')>Gratis</option>
                            <option value="paid" @selected(($filters['price'] ?? null) === 'paid')>Berbayar</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <label class="label" for="filter-rating">Rating minimal</label>
                        <select id="filter-rating" name="rating" class="select">
                            <option value="">Semua rating</option>
                            @foreach ([4, 3] as $rating)
                                <option value="{{ $rating }}" @selected(($filters['rating'] ?? null) == $rating)>{{ $rating }} bintang ke atas</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <label class="label" for="filter-sort">Urutkan</label>
                        <select id="filter-sort" name="sort" class="select">
                            <option value="newest" @selected(($filters['sort'] ?? null) === 'newest')>Terbaru</option>
                            <option value="popular" @selected(($filters['sort'] ?? null) === 'popular')>Terpopuler</option>
                            <option value="rating" @selected(($filters['sort'] ?? null) === 'rating')>Rating tertinggi</option>
                            <option value="price_low" @selected(($filters['sort'] ?? null) === 'price_low')>Harga terendah</option>
                            <option value="price_high" @selected(($filters['sort'] ?? null) === 'price_high')>Harga tertinggi</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-ui.button type="submit" class="w-full">Terapkan</x-ui.button>
                        <x-ui.button :href="route('courses.index')" variant="outline">Reset</x-ui.button>
                    </div>
                </section>
            </form>

            {{-- Hasil --}}
            <div>
                @include('public._catalog_results', ['courses' => $courses, 'filters' => $filters])
            </div>
        </div>
    </div>
</x-layouts.guest>
