import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/css/vendors.css',
                'resources/assets/sass/app.scss',
                'resources/assets/sass/auth.scss',
                'resources/assets/js/app.js',
                'resources/assets/js/auth.js',
                'resources/assets/js/iframe-contentWindow.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        // moment's package.json points `jsnext:main` at an ESM build and Vite follows
        // it (webpack did not). The CommonJS `require('moment')` inside
        // bootstrap-daterangepicker then receives a module namespace instead of the
        // function. Resolve bare `moment` to its CommonJS entry, as before.
        alias: [{ find: /^moment$/, replacement: 'moment/moment.js' }],
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // DDEV sets VITE_HMR_HOST to the project hostname (see .ddev/config.yaml);
        // Docker Compose maps 5173 straight to localhost.
        hmr: { host: process.env.VITE_HMR_HOST ?? 'localhost' },
    },
    css: {
        // The vendored theme and DataTables CSS carry IE-era `*property` hacks.
        // LightningCSS (Vite's minifier) rejects them; recovery strips them,
        // which is what every current browser does anyway.
        lightningcss: { errorRecovery: true },
    },
    build: { sourcemap: true },
});
