@props([
    'id' => null,
    'title' => 'Konfirmasi tindakan',
    'description' => null,
    'action',
    'method' => 'DELETE',
    'confirmLabel' => 'Lanjutkan',
])

@php
    $id = $id ?? 'confirm-'.Illuminate\Support\Str::random(8);
@endphp

<dialog id="{{ $id }}" class="alert-dialog" aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-description">
    <div>
        <header>
            <h2 id="{{ $id }}-title">{{ $title }}</h2>
            <p id="{{ $id }}-description">{{ $description ?? $slot }}</p>
        </header>

        <footer>
            <button type="button" class="btn" data-variant="outline" onclick="this.closest('dialog').close()">Batal</button>

            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)
                <button type="submit" class="btn" data-variant="destructive">{{ $confirmLabel }}</button>
            </form>
        </footer>
    </div>
</dialog>
