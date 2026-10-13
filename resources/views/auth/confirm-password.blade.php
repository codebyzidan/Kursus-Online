<x-layouts.auth title="Konfirmasi kata sandi" heading="Konfirmasi kata sandi" description="Masukkan kata sandi Anda untuk melanjutkan.">
    <form method="POST" action="{{ route('password.confirm') }}" class="grid gap-4">
        @csrf

        <x-ui.input name="password" type="password" label="Kata sandi" required autofocus autocomplete="current-password" />

        <x-ui.button type="submit" class="w-full">Konfirmasi</x-ui.button>
    </form>
</x-layouts.auth>
