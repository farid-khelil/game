import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/game.css',
                'resources/js/game.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Prevent tree-shaking from removing Alpine
        rollupOptions: {
            output: {
                // Keep module structure for better debugging
                manualChunks: undefined,
            },
        },
    },
});
