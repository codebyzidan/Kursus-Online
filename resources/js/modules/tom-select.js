import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

/**
 * Inisialisasi <select data-tom-select>. Bila ada data-search-url,
 * opsi dimuat dari server secara remote (JSON [ {value,text} ]).
 */
export function initSelects(root = document) {
    root.querySelectorAll('select[data-tom-select]:not([data-tom-select-ready])').forEach((el) => {
        el.dataset.tomSelectReady = 'true';

        const remote = el.dataset.searchUrl;
        const isMultiple = el.multiple;

        new TomSelect(el, {
            placeholder: el.dataset.placeholder || 'Cari…',
            allowEmptyOption: !el.required,
            maxItems: isMultiple ? null : 1,
            plugins: isMultiple ? ['remove_button'] : [],
            ...(remote
                ? {
                      valueField: 'value',
                      labelField: 'text',
                      searchField: ['text'],
                      loadThrottle: 300,
                      preload: 'focus',
                      load(query, callback) {
                          fetch(`${remote}?q=${encodeURIComponent(query)}`, {
                              headers: { Accept: 'application/json' },
                          })
                              .then((response) => (response.ok ? response.json() : []))
                              .then((data) => callback(data))
                              .catch(() => callback());
                      },
                  }
                : {}),
        });
    });
}
