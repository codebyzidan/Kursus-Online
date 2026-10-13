<x-layouts.app title="Kuis baru">
    <x-ui.page-header
        title="Kuis baru"
        description="Pilih kursus lalu atur kriteria penilaian."
        :items="['Dashboard instruktur' => route('instructor.dashboard'), 'Kuis' => route('instructor.quizzes.index'), 'Baru' => null]"
    />

    <form method="POST" action="{{ route('instructor.quizzes.store') }}" class="card">
        <section class="grid gap-5 p-5">
            @csrf

            <x-ui.select name="course_id" label="Kursus" :options="$courses->pluck('title', 'id')" :selected="old('course_id')" placeholder="Pilih kursus" required />
            <x-ui.input name="title" label="Judul kuis" :value="old('title')" required />
            <x-ui.textarea name="description" label="Deskripsi" rows="3">{{ old('description') }}</x-ui.textarea>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.input name="passing_grade" type="number" label="Nilai lulus" :value="old('passing_grade', 70)" min="0" max="100" required />
                <x-ui.input name="max_attempts" type="number" label="Batas percobaan" :value="old('max_attempts', 3)" min="1" hint="Kosongkan untuk tanpa batas." />
                <x-ui.input name="duration_minutes" type="number" label="Durasi (menit)" :value="old('duration_minutes')" min="1" hint="Kosongkan bila tanpa batas waktu." />
            </div>

            <div class="flex items-center gap-4">
                <x-ui.checkbox name="is_final" label="Jadikan kuis akhir kursus" :checked="old('is_final')" />
                <x-ui.checkbox name="is_published" label="Terbitkan" :checked="old('is_published', true)" />
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button type="submit">Simpan & tambah soal</x-ui.button>
                <x-ui.button :href="route('instructor.quizzes.index')" variant="outline">Batal</x-ui.button>
            </div>
        </section>
    </form>
</x-layouts.app>
