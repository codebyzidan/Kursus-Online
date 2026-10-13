<x-layouts.app title="Kursus baru">
    <x-ui.page-header
        title="Kursus baru"
        description="Isi informasi dasar kursus. Kurikulum dan harga dapat dilengkapi setelahnya."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kelola kursus' => route('instructor.courses.index'), 'Baru' => null]"
    />

    <form method="POST" action="{{ route('instructor.courses.store') }}" class="card">
        <section class="grid gap-5 p-5">
            @csrf

            <x-ui.input name="title" label="Judul kursus" :value="old('title')" required hint="Judul singkat dan jelas." />
            <x-ui.input name="subtitle" label="Subjudul" :value="old('subtitle')" hint="Ringkasan satu baris tentang kursus." />
            <x-ui.textarea name="description" label="Deskripsi" rows="6">{{ old('description') }}</x-ui.textarea>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.select
                    name="category_id"
                    label="Kategori"
                    :options="$categories->pluck('name', 'id')"
                    :selected="old('category_id')"
                    placeholder="Pilih kategori"
                />
                <x-ui.select
                    name="level"
                    label="Level"
                    :options="collect(\App\Enums\CourseLevel::cases())->mapWithKeys(fn ($level) => [$level->value => $level->label()])"
                    :selected="old('level', 'beginner')"
                    required
                />
            </div>

            <x-ui.input name="price" type="number" label="Harga (Rp)" :value="old('price', 0)" required min="0" hint="Isi 0 untuk kursus gratis." />

            <div class="flex items-center gap-2">
                <x-ui.button type="submit">Simpan & lanjut ke kurikulum</x-ui.button>
                <x-ui.button :href="route('instructor.courses.index')" variant="outline">Batal</x-ui.button>
            </div>
        </section>
    </form>
</x-layouts.app>
