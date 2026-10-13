{{-- Terapkan tema tersimpan sebelum paint agar tidak berkedip. --}}
<script>
    (function () {
        try {
            var mode = localStorage.getItem('themeMode');
            if (!mode) {
                mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    })();
</script>
