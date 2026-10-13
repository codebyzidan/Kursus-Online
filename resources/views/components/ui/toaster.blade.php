@php
    $flashes = [
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('info'),
    ];

    $flashes = array_filter($flashes, fn ($message) => filled($message));

    $meta = [
        'success' => ['Berhasil', 'circle-check'],
        'error' => ['Gagal', 'circle-x'],
        'warning' => ['Perhatian', 'triangle-alert'],
        'info' => ['Informasi', 'info'],
    ];
@endphp

<div id="toaster" class="toaster">
    @foreach ($flashes as $category => $message)
        <div
            class="toast"
            role="{{ $category === 'error' ? 'alert' : 'status' }}"
            aria-atomic="true"
            aria-hidden="false"
            data-category="{{ $category }}"
        >
            <div class="toast-content">
                <x-dynamic-component :component="'lucide-'.$meta[$category][1]" class="size-6" />
                <section>
                    <h2>{{ $meta[$category][0] }}</h2>
                    <p>{{ $message }}</p>
                </section>
                <footer>
                    <button type="button" class="btn" data-variant="outline" data-size="sm" data-toast-action>Tutup</button>
                </footer>
            </div>
        </div>
    @endforeach
</div>
