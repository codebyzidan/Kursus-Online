<x-layouts.guest :title="'Syarat layanan'">
    <div class="mx-auto w-full max-w-3xl px-4 py-12 sm:px-6">
        <x-ui.page-header title="Syarat layanan" :items="['Beranda' => route('home'), 'Syarat layanan' => null]" />

        <div class="card mt-6">
            <section class="grid gap-4 p-6 text-sm leading-relaxed">
                <p class="text-muted-foreground">
                    Materi kursus dilindungi hak cipta instruktur. Anda dilarang mengunduh, menyalin, atau membagikan
                    materi berbayar tanpa izin tertulis.
                </p>
                <p class="text-muted-foreground">
                    Kebijakan pengembalian dana: {{ \App\Models\Setting::get('refund_policy', 'Pengajuan pengembalian dana mengikuti kebijakan yang ditetapkan admin.') }}
                </p>
                <p class="text-muted-foreground">
                    Kami dapat menangguhkan akun yang melanggar ketentuan ini atau menyalahgunakan layanan.
                </p>
            </section>
        </div>
    </div>
</x-layouts.guest>
