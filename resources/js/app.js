import '@fontsource-variable/geist';

import './bootstrap';

// Basecoat: runtime + seluruh komponen (auto-init saat DOMContentLoaded).
import 'basecoat-css/all';

import htmx from 'htmx.org';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import persist from '@alpinejs/persist';
import mask from '@alpinejs/mask';

import { initCharts, refreshCharts } from './modules/charts';
import { initDatepickers } from './modules/datepicker';
import { initSidebarBreakpointSync } from './modules/sidebar';
import { initSelects } from './modules/tom-select';
import { initSortables } from './modules/sortable';
import { toast } from './modules/toaster';

/*
|--------------------------------------------------------------------------
| Alpine.js
|--------------------------------------------------------------------------
*/
Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.plugin(persist);
Alpine.plugin(mask);

window.Alpine = Alpine;

Alpine.start();

/*
|--------------------------------------------------------------------------
| htmx + Bootstrap helper
|--------------------------------------------------------------------------
*/
window.htmx = htmx;
window.Kursus = {
    toast,
    refreshCharts,
    initDatepickers,
    initSelects,
    initSortables,
    initCharts,
    initSidebarBreakpointSync,
};

/** Inisialisasi ulang plugin yang bergantung pada DOM (setelah swap htmx). */
function reinit(root = document) {
    initDatepickers(root);
    initSelects(root);
    initCharts(root);
    initSortables(root);
    initSidebarBreakpointSync(root);
}

// Sertakan CSRF token pada setiap permintaan htmx.
document.addEventListener('htmx:configRequest', (event) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (token) {
        event.detail.headers['X-CSRF-TOKEN'] = token;
    }
});

// Setelah konten di-swap: aktifkan Basecoat + plugin pada elemen baru.
document.addEventListener('htmx:afterSwap', (event) => {
    window.basecoat?.initAll?.();
    reinit(event.detail.target ?? document);
});

// Gambar ulang chart saat tema berganti agar warna CSS variable ikut menyesuaikan.
document.addEventListener('basecoat:themechange', () => refreshCharts());

document.addEventListener('DOMContentLoaded', () => reinit(document));
