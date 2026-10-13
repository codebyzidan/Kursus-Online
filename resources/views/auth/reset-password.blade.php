<x-layouts.auth title="Reset kata sandi" heading="Atur ulang kata sandi" description="Tentukan kata sandi baru untuk akun Anda.">
    <form method="POST" action="{{ route('password.store') }}" class="grid gap-4" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf
        @method('POST')

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-ui.input name="email" type="email" label="Email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        <x-ui.input name="password" type="password" label="Kata sandi baru" required autocomplete="new-password" />
        <x-ui.input name="password_confirmation" type="password" label="Ulangi kata sandi" required autocomplete="new-password" />

        <x-ui.button type="submit" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Simpan kata sandi</span>
            <span x-show="submitting" class="flex items-center gap-2">
                <x-lucide-loader-circle class="size-4 animate-spin" /> Memproses…
            </span>
        </x-ui.button>
    </form>
</x-layouts.auth>
