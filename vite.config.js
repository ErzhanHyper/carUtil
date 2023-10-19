import { defineConfig } from 'vite';
import vue from "@vitejs/plugin-vue";
import laravel from 'laravel-vite-plugin';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
import path from "path";

export default defineConfig({
    plugins: [
        vue({
            template: { transformAssetUrls }
        }),
        quasar({
        }),
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});
