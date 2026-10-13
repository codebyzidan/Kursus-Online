<x-layouts.app title="Pengguna">
    <x-ui.page-header
        title="Kelola pengguna"
        description="Atur peran dan status akun pengguna."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Pengguna' => null]"
    />

    <x-ui.filter-bar :action="route('admin.users.index')">
        <x-ui.search-input :query="request('q')" placeholder="Cari nama atau email" />
        <x-ui.filter-field>
            <x-ui.select name="role" :options="collect(\App\Enums\UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])" :selected="request('role')" placeholder="Semua peran" />
        </x-ui.filter-field>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
    </x-ui.filter-bar>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Pengguna</th><th>Peran & status</th><th>Bergabung</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <x-ui.avatar :src="$user->avatar_url" :name="$user->name" size="sm" />
                                        <div>
                                            <div class="text-sm font-medium">{{ $user->name }}</div>
                                            <div class="text-muted-foreground text-xs">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="select">
                                            @foreach (\App\Enums\UserRole::cases() as $role)
                                                <option value="{{ $role->value }}" @selected($user->hasRole($role->value))>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                        <label class="flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_active" value="1" role="switch" @checked($user->is_active)>
                                            Aktif
                                        </label>
                                        <x-ui.button type="submit" size="sm" variant="outline">Simpan</x-ui.button>
                                    </form>
                                </td>
                                <td class="text-muted-foreground">{{ $user->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="text-end">
                                    @if ($user->getKey() !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="ghost" size="icon-sm" aria-label="Hapus pengguna"><x-lucide-trash-2 class="size-4" /></x-ui.button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><x-ui.empty-state icon="users-round" title="Tidak ada pengguna" description="Belum ada pengguna yang cocok dengan filter." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $users->links() }}</div>
</x-layouts.app>
