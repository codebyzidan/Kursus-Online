/**
 * Menampilkan toast Basecoat dari sisi klien.
 * Kategori: success | error | info | warning.
 *
 * @param {{title?: string, description?: string, category?: string, duration?: number}} config
 */
export function toast(config = {}) {
    const toaster = document.querySelector('#toaster');

    if (toaster && typeof toaster.toast === 'function') {
        toaster.toast({ duration: 5000, ...config });
    }
}
