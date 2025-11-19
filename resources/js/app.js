import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Import jQuery and make it available globally
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Summernote CSS and JS
import 'summernote/dist/summernote-lite.min.css';
import 'summernote/dist/summernote-lite.min.js';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'InvestPro';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        vueApp.mount(el);
        
        return vueApp;
    },
    progress: {
        color: '#4f46e5',
    },
});