<x-layouts.auth title="Masuk" heading="Masuk ke akun Anda" description="Gunakan email dan kata sandi Anda.">
    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="grid gap-4" x-data="{ submitting: false }" x-on:submit="submitting = true">
        @csrf

        <x-ui.input name="email" type="email" label="Email" :value="old('email')" required autofocus autocomplete="username" />
        <x-ui.input name="password" type="password" label="Kata sandi" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <x-ui.checkbox name="remember" label="Ingat saya" />
            <a href="{{ route('password.request') }}" class="text-sm underline-offset-4 hover:underline">Lupa kata sandi?</a>
        </div>

        <x-ui.button type="submit" class="w-full" x-bind:disabled="submitting">
            <span x-show="!submitting">Masuk</span>
            <span x-show="submitting" class="flex items-center gap-2">
                <x-lucide-loader-circle class="size-4 animate-spin" /> Memproses…
            </span>
        </x-ui.button>
    </form>

    <div class="relative">
        <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-border"></span></div>
        <div class="relative flex justify-center text-xs uppercase"><span class="bg-card text-muted-foreground px-2">atau</span></div>
    </div>

    <x-ui.button :href="route('auth.google')" variant="outline" class="w-full">
        <x-lucide-chrome class="size-4" data-icon="inline-start" /> Masuk dengan Google
    </x-ui.button>

    <x-slot:footer>
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-foreground underline underline-offset-4">Daftar sekarang</a>
    </x-slot:footer>
</x-layouts.auth>
