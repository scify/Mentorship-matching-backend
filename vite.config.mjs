import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// DDEV sets VITE_HMR_HOST to the project hostname (see .ddev/config.yaml). Its router
// terminates TLS on port 5173 and forwards to the container, so the page (https) must
// load dev assets over https and open the HMR socket over wss. Docker Compose maps
// 5173 straight to localhost and needs none of this.
const hmrHost = process.env.VITE_HMR_HOST;

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
        ...(hmrHost
            ? {
                  hmr: { host: hmrHost, protocol: 'wss', clientPort: 5173 },
                  allowedHosts: [hmrHost],
                  cors: { origin: `https://${hmrHost}` },
              }
            : { hmr: { host: 'localhost' } }),
    },
    css: {
        // The vendored theme and DataTables CSS carry IE-era `*property` hacks.
        // LightningCSS (Vite's minifier) rejects them; recovery strips them,
        // which is what every current browser does anyway.
        lightningcss: { errorRecovery: true },
    },
    build: { sourcemap: true },
});
