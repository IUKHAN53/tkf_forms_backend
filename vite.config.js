import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                // Admin layout chrome — loaded on every admin page.
                'resources/css/admin/layout.css',
                // Page-scoped stylesheets. Kept as separate entries (rather than
                // folded into admin.css) so each still loads only on the page
                // that pushes it, as it did when it was an inline <style>.
                'resources/css/admin/core-forms.css',
                'resources/css/admin/uc-detail.css',
                'resources/css/admin/fixed-site-report.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
