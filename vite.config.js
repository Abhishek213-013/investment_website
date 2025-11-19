import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        cors: true,
    },
    optimizeDeps: {
        include: ['jquery', 'summernote']
    },
    build: {
        commonjsOptions: {
            include: [/node_modules/],
        },
    },
});