<x-layouts.auth title="Daftar" heading="Buat akun baru" description="Mulai belajar dalam satu menit.">
    <form method="POST" action="{{ route('register') }}" class="grid gap-4" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <x-ui.input name="name" label="Nama lengkap" :value="old('name')" required autofocus autocomplete="name" />
        <x-ui.input name="email" type="email" label="Email" :value="old('email')" required autocomplete="username" />
        <x-ui.input name="password" type="password" label="Kata sandi" required autocomplete="new-password" hint="Minimal 8 karakter." />
        <x-ui.input name="password_confirmation" type="password" label="Ulangi kata sandi" required autocomplete="new-password" />

        <x-ui.checkbox name="terms" label="Saya menyetujui syarat layanan dan kebijakan privasi." required />

        <x-ui.button type="submit" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Daftar</span>
            <span x-show="submitting" class="flex items-center gap-2">
                <x-lucide-loader-circle class="size-4 animate-spin" /> Memproses…
            </span>
        </x-ui.button>
    </form>

    <x-slot:footer>
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-foreground underline underline-offset-4">Masuk</a>
    </x-slot:footer>
</x-layouts.auth>
