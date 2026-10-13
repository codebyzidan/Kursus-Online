<x-layouts.guest :title="'Kebijakan privasi'">
    <div class="mx-auto w-full max-w-3xl px-4 py-12 sm:px-6">
        <x-ui.page-header title="Kebijakan privasi" :items="['Beranda' => route('home'), 'Kebijakan privasi' => null]" />

        <div class="card mt-6">
            <section class="grid gap-4 p-6 text-sm leading-relaxed">
                <p class="text-muted-foreground">
                    Kami mengumpulkan data seperlunya untuk menjalankan layanan: nama, alamat email, dan data transaksi.
                    Data digunakan untuk memberi akses kursus, menerbitkan sertifikat, dan mengirim pemberitahuan terkait pesanan.
                </p>
                <p class="text-muted-foreground">
                    Kami tidak menjual data pribadi Anda kepada pihak ketiga. Data pembayaran diproses oleh penyedia gateway
                    dan tidak disimpan pada server kami.
                </p>
                <p class="text-muted-foreground">
                    Anda dapat meminta penghapusan akun dan data pribadi dengan menghubungi
                    {{ \App\Models\Setting::get('contact_email', 'halo@kursus.test') }}.
                </p>
                <p class="text-muted-foreground">
                    Dengan mendaftar, Anda menyetujui pemrosesan data sesuai kebijakan ini dan prinsip perlindungan data pribadi (UU PDP).
                </p>
            </section>
        </div>
    </div>
</x-layouts.guest>
