import Sortable from 'sortablejs';

/**
 * Inisialisasi [data-sortable] dengan penyimpanan urutan otomatis.
 * Anak wajib punya atribut data-sortable-id.
 */
export function initSortables(root = document) {
    root.querySelectorAll('[data-sortable]:not([data-sortable-ready])').forEach((el) => {
        el.dataset.sortableReady = 'true';

        const handle = el.dataset.sortableHandle;

        Sortable.create(el, {
            animation: 150,
            handle: handle && handle !== 'false' ? handle : undefined,
            draggable: '[data-sortable-id]',
            ghostClass: 'opacity-40',
            onEnd: () => persist(el),
        });
    });
}

function persist(el) {
    const url = el.dataset.sortableUrl;

    if (!url) {
        return;
    }

    const order = Array.from(el.querySelectorAll('[data-sortable-id]')).map(
        (node) => node.dataset.sortableId,
    );

    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ order }),
    }).catch(() => {});
}
