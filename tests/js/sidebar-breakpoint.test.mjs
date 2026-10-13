import { test } from 'node:test';
import assert from 'node:assert/strict';
import { initSidebarBreakpointSync } from '../../resources/js/modules/sidebar.js';

/**
 * Menyiapkan sidebar bohongan + matchMedia bohongan, lalu memasang
 * sinkronisasi breakpoint. Mengembalikan helper untuk melintasi breakpoint.
 */
function setup({ initialized = true } = {}) {
    const changeListeners = [];
    const media = {
        matches: true,
        addEventListener(type, callback) {
            if (type === 'change') changeListeners.push(callback);
        },
        removeEventListener() {},
    };

    globalThis.window = { matchMedia: () => media };

    const calls = [];
    const initListeners = [];

    const sidebar = {
        dataset: initialized ? { sidebarInitialized: 'true' } : {},
        open() {
            calls.push('open');
        },
        close() {
            calls.push('close');
        },
        addEventListener(type, callback) {
            if (type === 'basecoat:initialized') initListeners.push(callback);
        },
    };

    const root = { querySelector: () => sidebar };

    initSidebarBreakpointSync(root);

    return {
        calls,
        sidebar,
        root,
        crossTo: (isDesktop) => changeListeners.forEach((callback) => callback({ matches: isDesktop })),
        markInitialized: () => {
            sidebar.dataset.sidebarInitialized = 'true';
            initListeners.forEach((callback) => callback());
        },
    };
}

test('mobile → collapse → desktop: sidebar dibuka kembali', () => {
    const { calls, crossTo } = setup();

    // Sidebar dalam keadaan tertutup (hasil collapse di mobile), lalu viewport
    // diperbesar melewati breakpoint ke desktop.
    crossTo(true);

    assert.deepEqual(calls, ['open']);
});

test('mobile (terbuka) → desktop: sidebar tampil normal', () => {
    const { calls, crossTo } = setup();

    crossTo(true);

    assert.deepEqual(calls, ['open']);
});

test('desktop → mobile: sidebar ditutup sesuai perilaku drawer mobile', () => {
    const { calls, crossTo } = setup();

    crossTo(false);

    assert.deepEqual(calls, ['close']);
});

test('desktop → mobile → desktop berulang: tidak ada state tertinggal', () => {
    const { calls, crossTo } = setup();

    crossTo(false);
    crossTo(true);
    crossTo(false);
    crossTo(true);

    assert.deepEqual(calls, ['close', 'open', 'close', 'open']);
});

test('perubahan ukuran tanpa melewati breakpoint tidak mengubah state', () => {
    const { calls, crossTo } = setup();

    // Berada di desktop lalu melebar lagi (matchMedia tidak memancarkan change).
    // Tidak ada panggilan reset => collapse manual desktop tetap berfungsi.
    assert.deepEqual(calls, []);

    // Sanity: handler hanya bereaksi pada event perubahan breakpoint.
    crossTo(true);
    assert.deepEqual(calls, ['open']);
});

test('sync tidak dipasang ganda walaupun diinisialisasi berulang', () => {
    const { calls, crossTo, root } = setup();

    initSidebarBreakpointSync(root);
    initSidebarBreakpointSync(root);

    crossTo(true);

    assert.deepEqual(calls, ['open']);
});

test('menunggu basecoat:initialized sebelum memasang listener', () => {
    const { calls, crossTo, markInitialized } = setup({ initialized: false });

    // Belum siap: belum ada listener breakpoint.
    crossTo(true);
    assert.deepEqual(calls, []);

    // Basecoat selesai inisialisasi, baru sinkronisasi aktif.
    markInitialized();
    crossTo(true);
    assert.deepEqual(calls, ['open']);
});
