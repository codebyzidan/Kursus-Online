import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

flatpickr.localize(Indonesian);

/**
 * Inisialisasi input tanggal pada elemen [data-flatpickr].
 * Nilai terkirim format `Y-m-d`, tampilan format `d M Y` (AGENTS.md §3).
 */
export function initDatepickers(root = document) {
    root.querySelectorAll('input[data-flatpickr]:not([data-flatpickr-ready])').forEach((el) => {
        el.dataset.flatpickrReady = 'true';

        flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            allowInput: true,
            locale: Indonesian,
            mode: el.dataset.flatpickrMode || 'single',
            ...(el.dataset.minDate ? { minDate: el.dataset.minDate } : {}),
            ...(el.dataset.maxDate ? { maxDate: el.dataset.maxDate } : {}),
        });
    });
}
