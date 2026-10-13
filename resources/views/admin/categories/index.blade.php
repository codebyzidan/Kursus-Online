<x-layouts.app title="Kategori">
    <x-ui.page-header
        title="Kategori"
        description="Kelola kategori kursus."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Kategori' => null]"
    />

    <div class="card">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <section class="grid gap-4 p-5 sm:grid-cols-4">
                <x-ui.input name="name" label="Nama kategori" required />
                <x-ui.select name="parent_id" label="Induk (opsional)" :options="$parents->pluck('name', 'id')" placeholder="Tanpa induk" />
                <x-ui.input name="icon" label="Ikon (nama Lucide)" placeholder="code" />
                <x-ui.input name="position" type="number" label="Urutan" :value="0" min="0" />
                <div class="sm:col-span-4">
                    <x-ui.button type="submit">Tambah kategori</x-ui.button>
                </div>
            </section>
        </form>
    </div>

    <div class="card">
        <section class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Nama</th><th>Slug</th><th>Induk</th><th>Kursus</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="font-medium">{{ $category->name }}</td>
                                <td class="text-muted-foreground">{{ $category->slug }}</td>
                                <td class="text-muted-foreground">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="tabular-nums">{{ $category->courses_count }}</td>
                                <td><x-ui.badge :variant="$category->is_active ? 'success' : 'neutral'">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                                <td class="text-end">
                                    <details class="inline-block text-start">
                                        <summary class="btn" data-variant="ghost" data-size="sm">Edit</summary>
                                        <div class="bg-card border-border mt-2 grid gap-3 rounded-md border p-4">
                                            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="grid gap-3">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.input name="name" label="Nama" :value="$category->name" required />
                                                <x-ui.select name="parent_id" label="Induk" :options="$parents->where('id', '!=', $category->id)->pluck('name', 'id')" :selected="$category->parent_id" placeholder="Tanpa induk" />
                                                <x-ui.input name="icon" label="Ikon" :value="$category->icon" />
                                                <x-ui.input name="position" type="number" label="Urutan" :value="$category->position" min="0" />
                                                <x-ui.checkbox name="is_active" label="Aktif" :checked="$category->is_active" />
                                                <x-ui.button type="submit" size="sm">Simpan</x-ui.button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-ui.empty-state icon="folder-tree" title="Belum ada kategori" description="Tambahkan kategori pertama untuk mengelompokkan kursus." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div>{{ $categories->links() }}</div>
</x-layouts.app>
