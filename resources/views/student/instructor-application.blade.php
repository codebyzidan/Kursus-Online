<x-layouts.app title="Pengajuan instruktur">
    <x-ui.page-header
        title="Menjadi instruktur"
        description="Ajukan diri untuk mengajar di {{ config('app.name') }}."
        :items="['Dashboard' => route('dashboard'), 'Pengajuan instruktur' => null]"
    />

    @if ($application)
        <div class="card">
            <section class="grid gap-4 p-5">
                <div class="flex items-center gap-2">
                    <x-ui.badge :variant="match ($application->status) {
                        \App\Enums\InstructorApplicationStatus::Approved => 'success',
                        \App\Enums\InstructorApplicationStatus::Rejected => 'danger',
                        default => 'warning',
                    }">{{ $application->status->label() }}</x-ui.badge>
                    <span class="text-muted-foreground text-xs">Diajukan {{ $application->created_at?->translatedFormat('d M Y') }}</span>
                </div>

                <div class="grid gap-2 text-sm">
                    <div>
                        <div class="text-muted-foreground text-xs">Motivasi</div>
                        <p>{{ $application->motivation }}</p>
                    </div>
                    @if ($application->expertise)
                        <div>
                            <div class="text-muted-foreground text-xs">Keahlian</div>
                            <p>{{ $application->expertise }}</p>
                        </div>
                    @endif
                    @if ($application->rejection_reason)
                        <x-ui.alert variant="destructive" title="Catatan admin">{{ $application->rejection_reason }}</x-ui.alert>
                    @endif
                </div>

                @if ($application->status === \App\Enums\InstructorApplicationStatus::Rejected)
                    <p class="text-muted-foreground text-sm">Anda dapat mengajukan ulang dengan formulir di bawah.</p>
                @elseif ($application->status === \App\Enums\InstructorApplicationStatus::Pending)
                    <p class="text-muted-foreground text-sm">Pengajuan Anda sedang ditinjau admin.</p>
                @endif
            </section>
        </div>
    @endif

    @unless ($application && $application->status === \App\Enums\InstructorApplicationStatus::Pending)
        <form method="POST" action="{{ route('student.instructor-application.store') }}" class="card">
            <section class="grid gap-4 p-5">
                @csrf

                <x-ui.input name="headline" label="Headline" :value="old('headline', auth()->user()->headline)" hint="Contoh: Full-stack Developer & Pengajar" />
                <x-ui.textarea name="expertise" label="Keahlian" rows="2" hint="Pisahkan dengan koma, misal: Laravel, Vue, MySQL" />
                <x-ui.input name="portfolio_url" type="url" label="Tautan portofolio (opsional)" :value="old('portfolio_url')" />
                <x-ui.textarea name="motivation" label="Motivasi" rows="5" hint="Ceritakan pengalaman dan alasan Anda mengajar (minimal 30 karakter).">{{ old('motivation') }}</x-ui.textarea>

                <x-ui.button type="submit" class="w-fit">Kirim pengajuan</x-ui.button>
            </section>
        </form>
    @endunless
</x-layouts.app>
