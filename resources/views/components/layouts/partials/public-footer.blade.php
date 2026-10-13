<footer class="border-border mt-16 border-t">
    <div class="text-muted-foreground mx-auto grid w-full max-w-7xl gap-6 px-4 py-10 text-sm sm:px-6 md:grid-cols-3">
        <div class="grid gap-2">
            <div class="text-foreground flex items-center gap-2 font-semibold">
                <x-lucide-graduation-cap class="size-4" />
                <span>{{ config('app.name') }}</span>
            </div>
            <p>{{ \App\Models\Setting::get('site_tagline', 'Belajar keterampilan digital dari praktisi.') }}</p>
        </div>

        <nav class="grid gap-2" aria-label="Tautan">
            <span class="text-foreground font-medium">Jelajahi</span>
            <a href="{{ route('courses.index') }}" class="hover:text-foreground">Katalog kursus</a>
            <a href="{{ route('certificates.verify.form') }}" class="hover:text-foreground">Verifikasi sertifikat</a>
        </nav>

        <nav class="grid gap-2" aria-label="Kebijakan">
            <span class="text-foreground font-medium">Kebijakan</span>
            <a href="{{ route('pages.privacy') }}" class="hover:text-foreground">Kebijakan privasi</a>
            <a href="{{ route('pages.terms') }}" class="hover:text-foreground">Syarat layanan</a>
            <span>{{ \App\Models\Setting::get('contact_email', 'halo@kursus.test') }}</span>
        </nav>
    </div>
</footer>
