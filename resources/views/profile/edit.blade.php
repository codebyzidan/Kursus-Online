<x-layouts.app title="Profil">
    <x-ui.page-header
        title="Profil"
        description="Kelola informasi akun, foto, dan kata sandi Anda."
        :items="['Dashboard' => route('dashboard'), 'Profil' => null]"
    />

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Informasi profil --}}
        <div class="card">
            <section class="grid gap-5 p-5">
                <header class="grid gap-1">
                    <h2 class="font-medium">Informasi profil</h2>
                    <p class="text-muted-foreground text-sm">Perbarui nama, kontak, dan deskripsi singkat.</p>
                </header>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-3">
                        <x-ui.avatar :src="$user->avatar_url" :name="$user->name" size="lg" />
                        <x-ui.input name="avatar" type="file" label="Foto profil" accept="image/*" hint="Format JPG/PNG, maksimal 2 MB." />
                    </div>

                    <x-ui.input name="name" label="Nama lengkap" :value="old('name', $user->name)" required />
                    <x-ui.input name="email" type="email" label="Email" :value="old('email', $user->email)" required />
                    <x-ui.input name="phone" label="Nomor telepon" :value="old('phone', $user->phone)" />
                    <x-ui.input name="headline" label="Headline" :value="old('headline', $user->headline)" hint="Contoh: Full-stack Developer" />
                    <x-ui.textarea name="bio" label="Bio" rows="3">{{ old('bio', $user->bio) }}</x-ui.textarea>

                    <div class="flex items-center gap-2">
                        <x-ui.button type="submit">Simpan perubahan</x-ui.button>
                    </div>
                </form>
            </section>
        </div>

        <div class="grid gap-6">
            {{-- Ubah kata sandi --}}
            <div class="card">
                <section class="grid gap-5 p-5">
                    <header class="grid gap-1">
                        <h2 class="font-medium">Ubah kata sandi</h2>
                        <p class="text-muted-foreground text-sm">Gunakan kata sandi yang panjang dan unik.</p>
                    </header>

                    <form method="POST" action="{{ route('password.update') }}" class="grid gap-4">
                        @csrf
                        @method('PUT')

                        <x-ui.input name="current_password" type="password" label="Kata sandi saat ini" autocomplete="current-password" />
                        <x-ui.input name="password" type="password" label="Kata sandi baru" autocomplete="new-password" />
                        <x-ui.input name="password_confirmation" type="password" label="Ulangi kata sandi baru" autocomplete="new-password" />

                        <x-ui.button type="submit" class="w-fit">Perbarui kata sandi</x-ui.button>
                    </form>
                </section>
            </div>

            {{-- Hapus akun --}}
            <div class="card">
                <section class="grid gap-5 p-5">
                    <header class="grid gap-1">
                        <h2 class="font-medium">Hapus akun</h2>
                        <p class="text-muted-foreground text-sm">
                            Setelah dihapus, seluruh data akun tidak dapat dikembalikan.
                        </p>
                    </header>

                    <x-ui.button variant="danger" class="w-fit" onclick="document.getElementById('delete-account-dialog').showModal()">
                        Hapus akun
                    </x-ui.button>

                    <dialog id="delete-account-dialog" class="alert-dialog" aria-labelledby="delete-account-title">
                        <div>
                            <header>
                                <h2 id="delete-account-title">Hapus akun Anda?</h2>
                                <p>
                                    Tindakan ini permanen. Akun <strong>{{ $user->name }}</strong> beserta seluruh datanya akan dihapus.
                                </p>
                            </header>

                            <form method="POST" action="{{ route('profile.destroy') }}" class="grid gap-3 px-6 pb-2">
                                @csrf
                                @method('DELETE')

                                <x-ui.input name="password" type="password" label="Konfirmasi kata sandi" bag="userDeletion" />

                                <footer class="flex justify-end gap-2 pt-2">
                                    <button type="button" class="btn" data-variant="outline" onclick="this.closest('dialog').close()">Batal</button>
                                    <button type="submit" class="btn" data-variant="destructive">Ya, hapus akun</button>
                                </footer>
                            </form>
                        </div>
                    </dialog>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
