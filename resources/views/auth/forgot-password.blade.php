<x-layouts.auth title="Lupa kata sandi" heading="Lupa kata sandi?" description="Masukkan email Anda, kami kirimkan tautan reset.">
    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="grid gap-4">
        @csrf

        <x-ui.input name="email" type="email" label="Email" :value="old('email')" required autofocus />

        <x-ui.button type="submit" class="w-full">Kirim tautan reset</x-ui.button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="text-foreground underline underline-offset-4">Kembali ke halaman masuk</a>
    </x-slot:footer>
</x-layouts.auth>
