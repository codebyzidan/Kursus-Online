<x-layouts.app :title="'Edit: '.$course->title">
    <x-ui.page-header
        :title="$course->title"
        description="Perbarui informasi, harga, dan metadata kursus."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kelola kursus' => route('instructor.courses.index'), 'Edit' => null]"
    >
        <x-slot:actions>
            <x-ui.badge :variant="match ($course->status) {
                \App\Enums\CourseStatus::Published => 'success',
                \App\Enums\CourseStatus::PendingReview => 'warning',
                \App\Enums\CourseStatus::Rejected => 'danger',
                default => 'neutral',
            }">{{ $course->status->label() }}</x-ui.badge>
            <x-ui.button :href="route('instructor.courses.curriculum', $course)" variant="outline">Kurikulum</x-ui.button>
            <x-ui.button :href="route('courses.show', $course)" variant="outline" target="_blank">Pratinjau</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($course->status === \App\Enums\CourseStatus::Rejected && $course->rejection_reason)
        <x-ui.alert variant="destructive" title="Kursus ditolak">{{ $course->rejection_reason }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('instructor.courses.update', $course) }}" class="card">
        <section class="grid gap-5 p-5">
            @csrf
            @method('PATCH')

            <x-ui.input name="title" label="Judul kursus" :value="old('title', $course->title)" required />
            <x-ui.input name="subtitle" label="Subjudul" :value="old('subtitle', $course->subtitle)" />
            <x-ui.textarea name="description" label="Deskripsi" rows="6">{{ old('description', $course->description) }}</x-ui.textarea>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.select name="category_id" label="Kategori" :options="$categories->pluck('name', 'id')" :selected="old('category_id', $course->category_id)" placeholder="Pilih kategori" />
                <x-ui.select name="level" label="Level" :options="collect(\App\Enums\CourseLevel::cases())->mapWithKeys(fn ($level) => [$level->value => $level->label()])" :selected="old('level', $course->level->value)" required />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="price" type="number" label="Harga (Rp)" :value="old('price', $course->price)" required min="0" />
                <x-ui.input name="sale_price" type="number" label="Harga diskon (opsional)" :value="old('sale_price', $course->sale_price)" min="0" hint="Harus lebih rendah dari harga normal." />
            </div>

            <x-ui.input name="promo_video_url" type="url" label="URL video promo" :value="old('promo_video_url', $course->promo_video_url)" />

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="meta_title" label="Meta title (SEO)" :value="old('meta_title', $course->meta_title)" />
                <x-ui.input name="meta_description" label="Meta description (SEO)" :value="old('meta_description', $course->meta_description)" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button type="submit">Simpan perubahan</x-ui.button>
                <x-ui.button :href="route('instructor.courses.curriculum', $course)" variant="outline">Kelola kurikulum</x-ui.button>
            </div>
        </section>
    </form>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="card">
            <section class="grid gap-3 p-5">
                <h2 class="font-medium">Ajukan review</h2>
                <p class="text-muted-foreground text-sm">
                    Setelah kurikulum lengkap, ajukan kursus untuk diperiksa admin sebelum tayang.
                </p>
                <form method="POST" action="{{ route('instructor.courses.submit', $course) }}">
                    @csrf
                    <x-ui.button type="submit" :disabled="! in_array($course->status, [\App\Enums\CourseStatus::Draft, \App\Enums\CourseStatus::Rejected])">
                        Ajukan untuk review
                    </x-ui.button>
                </form>
            </section>
        </div>

        <div class="card">
            <section class="grid gap-3 p-5">
                <h2 class="font-medium">Hapus kursus</h2>
                <p class="text-muted-foreground text-sm">Menghapus kursus juga menghapus bagian dan materinya.</p>
                <x-ui.button variant="danger" class="w-fit" onclick="document.getElementById('delete-course-dialog').showModal()">Hapus kursus</x-ui.button>

                <dialog id="delete-course-dialog" class="alert-dialog" aria-labelledby="delete-course-title">
                    <div>
                        <header>
                            <h2 id="delete-course-title">Hapus kursus?</h2>
                            <p>Kursus <strong>{{ $course->title }}</strong> akan dihapus permanen.</p>
                        </header>
                        <footer>
                            <button type="button" class="btn" data-variant="outline" onclick="this.closest('dialog').close()">Batal</button>
                            <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" data-variant="destructive">Ya, hapus</button>
                            </form>
                        </footer>
                    </div>
                </dialog>
            </section>
        </div>
    </div>
</x-layouts.app>
