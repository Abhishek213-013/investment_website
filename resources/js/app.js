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
        
        // Initialize Summernote after Vue app is mounted
        setTimeout(() => {
            if (window.$ && window.$.fn.summernote) {
                $('.summernote-editor').each(function() {
                    if (!$(this).hasClass('summernote-initialized')) {
                        $(this).summernote({
                            height: 200,
                            toolbar: [
                                ['style', ['style']],
                                ['font', ['bold', 'underline', 'clear']],
                                ['fontname', ['fontname']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['table', ['table']],
                                ['insert', ['link', 'picture', 'video']],
                                ['view', ['fullscreen', 'codeview', 'help']]
                            ],
                            callbacks: {
                                onChange: function(contents) {
                                    // Update the Vue data model when content changes
                                    const event = new Event('input', { bubbles: true });
                                    this.dispatchEvent(event);
                                }
                            }
                        }).addClass('summernote-initialized');
                    }
                });
            }
        }, 100);
        
        return vueApp;
    },
    progress: {
        color: '#4f46e5',
    },
});