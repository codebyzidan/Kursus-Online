import Chart from 'chart.js/auto';

const instances = new WeakMap();

function cssVar(name, fallback = '') {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();

    return value || fallback;
}

/** Palet chart dibaca dari CSS variable, bukan hex tertulis. */
export function chartPalette() {
    return [
        cssVar('--chart-1', '#111'),
        cssVar('--chart-2', '#ccc'),
        cssVar('--chart-3', '#b08000'),
        cssVar('--chart-4', '#3b6ea5'),
        cssVar('--chart-5', '#7a5aa5'),
    ];
}

function applyTheme(config) {
    const palette = chartPalette();
    const text = cssVar('--muted-foreground', '#666');
    const grid = cssVar('--border', '#e5e5e5');
    const surface = cssVar('--card', '#fff');

    config.options = config.options || {};
    const options = config.options;

    options.responsive = true;
    options.maintainAspectRatio = options.maintainAspectRatio ?? false;
    options.interaction = Object.assign({ intersect: false, mode: 'index' }, options.interaction);

    options.plugins = options.plugins || {};
    options.plugins.legend = Object.assign(
        {
            labels: {
                color: text,
                usePointStyle: true,
                boxWidth: 8,
                boxHeight: 8,
                font: { size: 11 },
            },
        },
        options.plugins.legend,
    );
    options.plugins.tooltip = Object.assign(
        {
            backgroundColor: surface,
            titleColor: cssVar('--foreground', '#111'),
            bodyColor: text,
            borderColor: grid,
            borderWidth: 1,
            padding: 10,
            displayColors: false,
        },
        options.plugins.tooltip,
    );

    const cartesian = ['line', 'bar', 'radar'].includes(config.type);

    if (cartesian) {
        options.scales = options.scales || {};
        options.scales.x = Object.assign(
            {
                grid: { display: false },
                border: { display: false },
                ticks: { color: text, font: { size: 11 } },
            },
            options.scales.x,
        );
        options.scales.y = Object.assign(
            {
                beginAtZero: true,
                grid: { color: grid, drawTicks: false, borderDash: [4, 4] },
                border: { display: false },
                ticks: { color: text, font: { size: 11 }, padding: 8 },
            },
            options.scales.y,
        );
    }

    (config.data?.datasets ?? []).forEach((dataset, index) => {
        const color = palette[index % palette.length];

        if (config.type === 'line') {
            dataset.borderColor = dataset.borderColor || color;
            dataset.backgroundColor = dataset.backgroundColor || color;
            dataset.tension = dataset.tension ?? 0.35;
            dataset.borderWidth = dataset.borderWidth ?? 2;
            dataset.pointRadius = dataset.pointRadius ?? 0;
            dataset.pointHoverRadius = dataset.pointHoverRadius ?? 4;
        } else if (config.type === 'doughnut' || config.type === 'pie') {
            dataset.backgroundColor = dataset.backgroundColor || palette;
            dataset.borderColor = dataset.borderColor || surface;
            dataset.borderWidth = dataset.borderWidth ?? 2;
        } else {
            dataset.backgroundColor = dataset.backgroundColor || color;
            dataset.borderColor = dataset.borderColor || color;
            dataset.borderRadius = dataset.borderRadius ?? 6;
            dataset.maxBarThickness = dataset.maxBarThickness ?? 36;
        }
    });
}

/** Inisialisasi semua canvas[data-chart] di dalam root. */
export function initCharts(root = document) {
    root.querySelectorAll('canvas[data-chart]:not([data-chart-ready])').forEach((canvas) => {
        let config;

        try {
            config = JSON.parse(canvas.dataset.chart ?? '{}');
        } catch {
            return;
        }

        canvas.dataset.chartReady = 'true';

        applyTheme(config);

        instances.set(canvas, new Chart(canvas, config));
    });
}

/** Hancurkan chart agar bisa digambar ulang saat tema berubah. */
export function destroyCharts(root = document) {
    root.querySelectorAll('canvas[data-chart-ready]').forEach((canvas) => {
        instances.get(canvas)?.destroy();
        instances.delete(canvas);
        delete canvas.dataset.chartReady;
    });
}

export function refreshCharts() {
    destroyCharts(document);
    initCharts(document);
}
