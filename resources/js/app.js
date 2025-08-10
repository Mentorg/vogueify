import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ToastPlugin from 'vue-toast-notification';
import { createI18n } from 'vue-i18n';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import enPage from './i18n/en/page.json';
import enCommon from './i18n/en/common.json';
import frPage from './i18n/fr/page.json';
import frCommon from './i18n/fr/common.json';
import dePage from './i18n/de/page.json';
import deCommon from './i18n/de/common.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const messages = {
  en: {
    page: enPage,
    common: enCommon,
  },
  fr: {
    page: frPage,
    common: frCommon,
  },
  de: {
    page: dePage,
    common: deCommon,
  },
}

const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages
})

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ToastPlugin)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
