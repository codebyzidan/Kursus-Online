<x-layouts.app title="Sertifikat">
    <x-ui.page-header
        title="Sertifikat"
        description="Sertifikat penyelesaian kursus Anda."
        :items="['Dashboard' => route('dashboard'), 'Sertifikat' => null]"
    />

    @if ($certificates->isEmpty())
        <x-ui.empty-state icon="award" title="Belum ada sertifikat" description="Selesaikan kursus hingga 100% untuk mendapatkan sertifikat.">
            <x-slot:action>
                <x-ui.button :href="route('student.courses.index')">Lihat kursus saya</x-ui.button>
            </x-slot:action>
        </x-ui.empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($certificates as $certificate)
                <article class="card">
                    <section class="grid gap-3 p-5">
                        <span class="bg-muted text-muted-foreground flex size-10 items-center justify-center rounded-md">
                            <x-lucide-award class="size-5" />
                        </span>
                        <h2 class="text-sm font-semibold">{{ $certificate->enrollment->course?->title }}</h2>
                        <dl class="text-muted-foreground grid gap-1 text-xs">
                            <div class="flex justify-between"><dt>Nomor</dt><dd class="text-foreground">{{ $certificate->number }}</dd></div>
                            <div class="flex justify-between"><dt>Terbit</dt><dd class="text-foreground">{{ $certificate->issued_at?->translatedFormat('d M Y') }}</dd></div>
                        </dl>
                    </section>
                    <footer class="flex items-center gap-2 border-t border-border p-4">
                        <x-ui.button :href="route('student.certificates.download', $certificate)" size="sm">
                            <x-lucide-download class="size-4" data-icon="inline-start" /> Unduh PDF
                        </x-ui.button>
                        <x-ui.button :href="route('certificates.verify.form')" variant="outline" size="sm">Verifikasi</x-ui.button>
                    </footer>
                </article>
            @endforeach
        </div>

        <div>{{ $certificates->links() }}</div>
    @endif
</x-layouts.app>
