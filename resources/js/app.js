import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route as ziggyRoute, ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'SUPPLY4ME';

const CATALOG_PREFIXES = ['/shop', '/product/'];
const CATALOG_LOADED_AT = 'supply4me:catalog-loaded-at';
const CATALOG_STALE_AFTER = 60 * 1000;

const isCatalogPage = () => {
    const { pathname } = window.location;

    return CATALOG_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(prefix));
};

router.on('success', () => {
    if (!isCatalogPage()) {
        return;
    }

    try {
        window.localStorage.setItem(CATALOG_LOADED_AT, String(Date.now()));
    } catch (error) {}
});

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState !== 'visible' || !isCatalogPage()) {
        return;
    }

    let loadedAt;

    try {
        loadedAt = Number(window.localStorage.getItem(CATALOG_LOADED_AT) || 0);
    } catch (error) {
        return;
    }

    if (Date.now() - loadedAt < CATALOG_STALE_AFTER) {
        return;
    }

    router.reload({ preserveScroll: true });
});

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        window.route = (name, params, absolute) =>
            ziggyRoute(name, params, absolute, window.Ziggy);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, window.Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
