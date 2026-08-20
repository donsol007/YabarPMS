import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

document.addEventListener('livewire:navigated', () => {
    document.documentElement.classList.toggle('dark', localStorage.getItem('theme') === 'dark');
});

document.addEventListener('alpine:init', () => {
    if (! window.Alpine) {
        return;
    }

    window.Alpine.store('theme', {
        dark: localStorage.getItem('theme') === 'dark',

        init() {
            this.apply();
        },

        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            this.apply();
        },

        apply() {
            document.documentElement.classList.toggle('dark', this.dark);
        },
    });

    window.Alpine.data('toastHandler', () => ({
        toasts: [],

        init() {
            window.addEventListener('toast', (event) => this.add(event.detail));

            const flash = document.querySelector('[data-toast-flash]');

            if (flash && flash.dataset.toastFlash) {
                try {
                    this.add(JSON.parse(flash.dataset.toastFlash));
                } catch (e) {
                    // ignore malformed flash
                }
            }
        },

        add({ type = 'success', message }) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4500);
        },

        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
    }));

    window.Alpine.data('lgaDropdown', (initialState, initialLga, lgasByState) => ({
        lgas: [],

        init() {
            if (initialState && lgasByState[initialState]) {
                this.lgas = lgasByState[initialState];
            }
        },

        onStateChange(stateId) {
            this.lgas = (stateId && lgasByState[stateId]) ? lgasByState[stateId] : [];
        },
    }));

    window.Alpine.data('fileDrop', () => ({
        dragging: false,

        handleDrop(event, inputId) {
            this.dragging = false;

            if (event.dataTransfer.files.length) {
                document.getElementById(inputId).files = event.dataTransfer.files;
                document.getElementById(inputId).dispatchEvent(new Event('change', { bubbles: true }));
            }
        },
    }));

    window.Alpine.data('filePreview', () => ({
        url: null,
        isImage: false,

        open(file) {
            this.url = URL.createObjectURL(file);
            this.isImage = file.type.startsWith('image/');
        },

        close() {
            if (this.url) {
                URL.revokeObjectURL(this.url);
            }
            this.url = null;
        },
    }));

    window.Alpine.data('chart', () => ({
        chart: null,

        load(type, data) {
            const values = data.map((row) => Number(row.value));
            const labels = data.map((row) => row.label);

            if (values.length === 0 || values.every((v) => v === 0)) {
                this.$el.innerHTML = '<div class="flex h-full items-center justify-center text-sm text-slate-400">No data yet</div>';

                return;
            }

            const isDark = () => document.documentElement.classList.contains('dark');
            const options = this.build(type, labels, values, isDark());

            this.$watch('$store.theme.dark', () => {
                if (this.chart) {
                    this.chart.updateOptions(this.build(type, labels, values, isDark()), false, true);
                }
            });

            this.chart = new ApexCharts(this.$el, options);
            this.chart.render();
        },

        build(type, labels, values, dark) {
            const base = {
                chart: {
                    type,
                    height: 320,
                    toolbar: { show: false },
                    foreColor: dark ? '#94a3b8' : '#64748b',
                    fontFamily: 'Figtree, sans-serif',
                },
                theme: { mode: dark ? 'dark' : 'light' },
                dataLabels: { enabled: false },
                colors: ['#EB721E', '#10b981', '#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444', '#14b8a6', '#f97316'],
            };

            if (type === 'pie') {
                base.labels = labels;
                base.series = values;
                base.legend = { position: 'bottom', labels: { colors: dark ? '#cbd5e1' : '#475569' } };
                base.tooltip = {
                    y: { formatter: (v) => formatCurrency(v) },
                };
                base.plotOptions = {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { show: false },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: dark ? '#94a3b8' : '#64748b',
                                    formatter: (w) => formatCurrency(w.globals.seriesTotals.reduce((a, b) => a + b, 0)),
                                },
                            },
                        },
                    },
                };
                base.stroke = { width: 0 };
            } else {
                base.xaxis = { categories: labels, axisBorder: { color: dark ? '#1e293b' : '#e2e8f0' } };
                base.grid = { borderColor: dark ? '#1e293b' : '#e2e8f0' };
                base.yaxis = { labels: { formatter: (v) => formatCurrency(v) } };

                if (type === 'line') {
                    base.series = [{ name: 'Portfolio Value', data: values }];
                    base.stroke = { curve: 'smooth', width: 3, colors: ['#EB721E'] };
                    base.colors = ['#EB721E'];
                    base.markers = { size: 4, strokeWidth: 0 };
                    base.fill = {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.02,
                            stops: [0, 90, 100],
                        },
                    };
                } else {
                    base.series = [{ name: 'Investments', data: values }];
                    base.colors = ['#EB721E'];
                    base.plotOptions = { bar: { borderRadius: 4, columnWidth: '55%' } };
                    base.tooltip = { y: { formatter: (v) => formatCurrency(v) } };
                }
            }

            return base;
        },
    }));
});

function formatCurrency(value) {
    try {
        return new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN',
            maximumFractionDigits: 0,
        }).format(value);
    } catch (e) {
        return String(value);
    }
}