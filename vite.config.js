import path from 'node:path';

import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

import { defineConfig } from 'vite';

import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.ts',
            ],

            refresh: true,

            fonts: [
                bunny(
                    'Instrument Sans',
                    {
                        weights: [
                            400,
                            500,
                            600,
                            700,
                            800,
                        ],
                    },
                ),
            ],
        }),

        vue(),

        tailwindcss(),
    ],

    resolve: {
        alias: {
            '@': path.resolve(
                import.meta.dirname,
                'resources/js',
            ),
        },
    },

    server: {
        host: '127.0.0.1',
        port: 5174,
        strictPort: true,

        watch: {
            ignored: [
                '**/storage/framework/views/**',
            ],
        },
    },
});