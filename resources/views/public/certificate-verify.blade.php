<x-layouts.guest :title="'Verifikasi sertifikat'" :description="'Periksa keaslian sertifikat kursus dengan kode verifikasi.'">
    <div class="mx-auto w-full max-w-2xl px-4 py-12 sm:px-6">
        <x-ui.page-header
            title="Verifikasi sertifikat"
            description="Masukkan kode verifikasi yang tercantum pada sertifikat untuk memeriksa keasliannya."
            :items="['Beranda' => route('home'), 'Verifikasi sertifikat' => null]"
        />

        <form method="POST" action="{{ route('certificates.verify') }}" class="card mt-6">
            <section class="grid gap-4 p-5">
                @csrf
                <x-ui.input name="code" label="Kode verifikasi" placeholder="Contoh: A1B2C3D4E5F6" :value="old('code')" required maxlength="32" />

                <x-ui.button type="submit" class="w-fit">
                    <x-lucide-scan-search class="size-4" data-icon="inline-start" /> Verifikasi
                </x-ui.button>
            </section>
        </form>

        @if ($searched ?? false)
            @if ($certificate)
                <div class="card mt-6">
                    <section class="grid gap-4 p-6">
                        <div class="flex items-center gap-2">
                            <x-lucide-badge-check class="size-5 text-success" />
                            <h2 class="font-medium">Sertifikat terverifikasi</h2>
                        </div>

                        <dl class="grid gap-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-muted-foreground text-xs">Nomor sertifikat</dt>
                                <dd class="font-medium">{{ $certificate->number }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground text-xs">Tanggal terbit</dt>
                                <dd class="font-medium">{{ $certificate->issued_at?->translatedFormat('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground text-xs">Nama siswa</dt>
                                <dd class="font-medium">{{ $certificate->enrollment->user?->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground text-xs">Kursus</dt>
                                <dd class="font-medium">{{ $certificate->enrollment->course?->title }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-muted-foreground text-xs">Instruktur</dt>
                                <dd class="font-medium">{{ $certificate->enrollment->course?->instructor?->name }}</dd>
                            </div>
                        </dl>
                    </section>
                </div>
            @else
                <x-ui.alert variant="warning" title="Sertifikat tidak ditemukan" class="mt-6">
                    Kode verifikasi tidak cocok dengan sertifikat mana pun. Periksa kembali penulisannya.
                </x-ui.alert>
            @endif
        @endif
    </div>
</x-layouts.guest>
