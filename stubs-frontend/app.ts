import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { toast, confirm, directives } from '@/Plugins';
import { useLanguageStore } from '@/Stores/languageStore';
import AppLayout from '@/Layouts/AppLayout.vue';
import * as Components from '@/Components';

createInertiaApp({
    title: (title) => title ? `${title} - PMS` : 'PMS',

    resolve: (name) => {
        const pages = import.meta.glob<{ default?: DefineComponent } & Record<string, unknown>>('./Pages/**/*.vue', { eager: true });
        const pageModule = pages[`./Pages/${name}.vue`];

        if (!pageModule) {
            throw new Error(`Page "${name}" not found`);
        }

        const page = (pageModule.default || pageModule) as DefineComponent;

        if (page.layout === undefined) {
            page.layout = AppLayout as DefineComponent;
        }

        return page;
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Register global components (available in all templates without import)
        // Inertia.js global components
        app.component('Head', Head);
        app.component('Link', Link);

        // Custom global components
        Object.entries(Components).forEach(([name, component]) => {
            app.component(name, component);
        });

        // Use plugins
        app.use(plugin);
        app.use(createPinia());
        app.use(toast);
        app.use(confirm);
        app.use(directives);

        // Initialize language settings and set document attributes
        const languageStore = useLanguageStore();
        languageStore.initialize();

        if (typeof document !== 'undefined') {
            const direction = languageStore.isRTL ? 'rtl' : 'ltr';
            document.documentElement.dir = direction;
            document.documentElement.lang = languageStore.currentLanguage;
        }

        // Mount the app
        app.mount(el);
    },

    progress: {
        color: '#3b82f6',
        showSpinner: true
    }
});
