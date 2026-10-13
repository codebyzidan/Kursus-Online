<x-layouts.auth title="Verifikasi email" heading="Verifikasi email Anda" description="Kami telah mengirim tautan verifikasi ke email Anda.">
    @if (session('status') === 'verification-link-sent')
        <x-ui.alert variant="success" title="Tautan terkirim">
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </x-ui.alert>
    @endif

    <x-ui.alert variant="info">
        Sebelum melanjutkan, periksa email masuk Anda dan klik tautan verifikasi. Klik "Kirim ulang"
        bila belum menerima email.
    </x-ui.alert>

    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit">Kirim ulang email</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="outline">Keluar</x-ui.button>
        </form>
    </div>
</x-layouts.auth>
