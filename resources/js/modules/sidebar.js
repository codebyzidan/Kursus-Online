/**
 * Sinkronisasi state sidebar Basecoat dengan breakpoint responsif.
 *
 * Masalah: Basecoat (basecoat-css) memakai satu flag `open` untuk mobile & desktop
 * dan tidak memantau perubahan breakpoint. Akibatnya, menutup sidebar di mobile
 * menyetel `aria-hidden="true"` yang terbawa ke desktop, sehingga sidebar tetap
 * tersembunyi setelah viewport diperbesar.
 *
 * Perbaikan: reset state HANYA ketika breakpoint benar-benar berubah.
 *   - Masuk desktop => sidebar dibuka (terlihat, sesuai default desktop).
 *   - Masuk mobile  => sidebar ditutup (perilaku default drawer mobile).
 *
 * Karena reset tidak dijalankan untuk perubahan ukuran di dalam mode yang sama,
 * fitur collapse manual desktop tetap independen dan berfungsi normal.
 */
export function initSidebarBreakpointSync(root = document) {
    const sidebar = root.querySelector('.sidebar');

    if (!sidebar || sidebar.dataset.breakpointSync === 'true') {
        return;
    }

    const breakpoint = parseInt(sidebar.dataset.breakpoint || '768', 10) || 768;
    const media = window.matchMedia(`(min-width: ${breakpoint}px)`);

    const applyBreakpointState = (isDesktop) => {
        if (typeof sidebar.open !== 'function' || typeof sidebar.close !== 'function') {
            return;
        }

        if (isDesktop) {
            sidebar.open();
        } else {
            sidebar.close();
        }
    };

    const attach = () => {
        if (sidebar.dataset.breakpointSync === 'true') {
            return;
        }

        sidebar.dataset.breakpointSync = 'true';

        const handleChange = (event) => applyBreakpointState(event.matches);

        if (typeof media.addEventListener === 'function') {
            media.addEventListener('change', handleChange);
        } else {
            // Fallback untuk browser lama.
            media.addListener(handleChange);
        }
    };

    // Basecoat menandai komponen siap lewat `data-sidebar-initialized` dan
    // memancarkan event `basecoat:initialized`.
    if (sidebar.dataset.sidebarInitialized) {
        attach();
    } else {
        sidebar.addEventListener('basecoat:initialized', attach, { once: true });
    }
}
