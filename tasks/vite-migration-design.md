# Vite migration — design

Date: 2026-09-22
Status: implemented on branch `feat/vite` (2026-09-22); see `tasks/todo.md` Review

## Goal

Replace Laravel Mix (webpack 5) with Vite 8 and `laravel-vite-plugin`. Remove the webpack pin that
laravel-mix 6.0.49 forces (`webpack@5.105.4`; 5.106 and 5.107 break Mix, and Mix has no newer release).
Keep every page working exactly as it does today. No visual or behavioural change is in scope.

## Non-goals

- No Bootstrap upgrade. Bootstrap 3 JS and CSS stay.
- No rewrite of the jQuery controllers into ES modules. They keep their `window.X = function` shape.
- No fix for the ~50 broken `url(../../globals/img/...)` references in the theme CSS. They are broken
  today and stay broken. Vite warns and continues.
- `resources/views/mentors/forms/create_edit_old.blade.php` is not referenced by any controller or
  route. It is left untouched.

## Decisions (agreed with Paul, 2026-09-22)

| Topic | Decision |
|---|---|
| Inline Blade scripts | Add `type="module"` to every inline `<script>` block (17 live views; the dead `create_edit_old` is skipped). |
| Dev workflow | Vite dev server with HMR on port 5173. `watch` script kept as `vite build --watch`. |
| Bundle layout | One JS entry per layout: `app.js` (everything the backoffice needs, in order), `auth.js`, `iframe-contentWindow.js`. |
| `public/js/iframe.js` (iframeResizer host script) | Dropped. No view or known external page loads it. |
| Unused deps `vue`, `vue-resource`, `modernizr`, `npm-modernizr` | Removed. Nothing references them. |
| Absolute `/build/...` URLs in theme CSS/SCSS | Rewritten to relative paths so Vite emits the assets. No copy plugin. |

## Current state (what the design replaces)

`webpack.mix.js` produces:

- `public/css/vendors.css` — concatenation of 11 vendor CSS files (`mix.styles`).
- `public/css/app.css`, `public/css/auth.css` — Sass.
- `public/js/libs.js` — concatenation of 26 vendor and first-party scripts that share `window`.
- `public/js/app.js` — `resources/assets/js/app.js` (CommonJS `require` calls, Sentry init, ready block).
- `public/js/controllers.js` — 10 controller files, each assigning `window.XController`.
- `public/js/auth.js`, `public/js/iframe.js`, `public/js/iframe-contentWindow.js`.
- `public/js/manifest.js`, `public/js/vendor.js` — from `mix.extract`.
- Copies of fonts, icheck skins, chosen sprites and theme images into `public/fonts`, `public/build/*`,
  `public/css`.

Blade loads these through 11 `mix()` calls in `common/header/header.blade.php`, `common/footer.blade.php`,
`layouts/auth.blade.php`, `mentors/forms/create_edit.blade.php`, `mentees/forms/create_edit.blade.php`.

Facts that constrain the design:

- 12 of 19 first-party JS files use `$` without importing it. 8 assign `window.X = function`. The
  controllers do the same. Inline Blade scripts call `new window.XController()` and `$(...)`.
- Bootstrap 3, icheck, chosen, jasny-bootstrap, bootstrap-select, toastr, daterangepicker and the theme
  scripts read `window.jQuery` at load time.
- Vite emits `<script type="module">`, which is deferred. Classic inline scripts run before it.
- Vite does not transform `require()` in application code. 11 `require` calls exist in first-party files.
- Vite writes to `public/build/` and empties it on every build. Theme CSS references
  `/build/fontawesome/fonts/` (6×) and `/build/ionicons/fonts/` (5×) by absolute URL; `app.scss`
  references `/build/css/orange.png` (1×); `plugins.css` references `/build/css/chosen-sprite.png` (1×).
- `docker-compose.yml` already maps `5173:5173` on the `php` service. `.ddev/config.yaml` exposes nothing
  extra yet.
- `.npmrc` sets `ignore-scripts=true`, `engine-strict=true`, `legacy-peer-deps=true`. Vite 8 and
  Rolldown ship native binaries as optional dependencies, not postinstall scripts.
- `.github/workflows/ci.yml` `npm` job runs `npm ci` then `npm run prod`.
- The e2e suite (`e2e/tests/03-routes.spec.ts`) opens every page as every role but only checks HTTP
  status and server error markers. It does not detect JavaScript errors.

## Design

### 1. Module graph

New file `resources/assets/js/jquery-global.js`:

```js
import $ from 'jquery';
window.$ = window.jQuery = $;
```

Every JS entry imports this file first. ESM evaluates imports in source order, so `window.jQuery`
exists before any plugin module runs. Vite deduplicates the module, so it executes once per page.

`resources/assets/js/app.js` becomes a single ordered list of side-effect imports followed by the
existing ready block. The order is the order of today's `libs.js` array, then the theme modules, then
the first-party helpers, then the controllers. Vendor imports use explicit file paths, as
`webpack.mix.js` does today, so package `main` fields are irrelevant.

```js
import './jquery-global';
import _ from 'lodash'; window._ = _;                      // app.js:4 does this today
import 'jquery-validation/dist/jquery.validate.min.js';
import 'jquery-ui-dist/jquery-ui.min.js';
import 'icheck/icheck.js';  // .min assigns an undeclared `_determinate`; strict-mode ESM throws
import 'chosen-js/chosen.jquery.js';
import select2 from 'select2/dist/js/select2.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'velocity-animate/velocity.min.js';
import moment from 'moment';                               // window.moment for daterangepicker
import toastr from 'toastr';
import 'scrollmonitor/dist/module/index.js';
import 'textarea-autosize';  // its exports map only exposes the package root
import 'bootstrap-select/dist/js/bootstrap-select.min.js';
import 'fastclick/lib/fastclick.js';
import 'jasny-bootstrap/dist/js/jasny-bootstrap.min.js';
import 'sweetalert/dist/sweetalert.min.js';
import dataTables from 'datatables/media/js/jquery.dataTables.min.js';
import '../pleasure-admin-panel/js/sliders.js';
import { Layout } from '../pleasure-admin-panel/js/layout.js';
import { Pleasure } from '../pleasure-admin-panel/js/pleasure.js';
import 'bootstrap-daterangepicker/daterangepicker.js';
import { CustomFormsPickers } from '../pleasure-admin-panel/js/custom-forms-pickers.js';
import './FormController.js';
import './AvailabilityStatusChangeViewHandler.js';
import 'ion-rangeslider/js/ion.rangeSlider.min.js';
import './MentorsAndMenteesListsCssCorrector.js';
import './TabsHandler.js';
import './UniversityHandler.js';
import './ResidenceHandler.js';
import './ReferenceHandler.js';
import './controllers/CompaniesListController.js';
// ... the other 9 controllers, same order as webpack.mix.js
import * as Sentry from '@sentry/browser';
// existing Sentry init and $(document).ready block, unchanged except the env var name
```

Any vendor file that Mix loaded as a raw script but that internally does `require('jquery')` or
`require('moment')` (UMD wrappers) resolves to the same module instance as `jquery-global.js`, because
Vite serves one copy of each package.

The 11 `require()` calls in first-party files become `import`. `process.env.MIX_SENTRY_DSN_PUBLIC`
becomes `import.meta.env.VITE_SENTRY_DSN_PUBLIC`. `window.Popper = require('popper.js').default` is
kept as `import Popper from 'popper.js'; window.Popper = Popper;`. Nothing else in the helpers or
controllers changes.

Other entries:

- `resources/assets/js/auth.js`: `import './jquery-global'; import './AuthPage.js';`
- `resources/assets/js/iframe-contentWindow.js`: `import 'iframe-resizer/js/iframeResizer.contentWindow.min.js';`
- `resources/assets/css/vendors.css` (new): `@import` of the 11 files from `mix.styles`, same order.
- `resources/assets/sass/app.scss`, `resources/assets/sass/auth.scss`: unchanged apart from one `url()`.

Dropped: `mix.extract` (Vite splits shared chunks), `hashFunction: 'sha256'` (Rolldown is native, not
wasm), `mix.autoload` (replaced by `jquery-global.js`), the `iframe.js` entry, all `mix.copy` calls.

### 2. `vite.config.mjs`

```js
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
            refresh: ['resources/views/**'],
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
```

`VITE_HMR_HOST` is set by DDEV (`web_environment`). When present, the hot-file URL becomes
`https://<host>:5173`, HMR uses `wss`, the hostname is allowed, and CORS answers for the page origin —
the DDEV router terminates TLS on 5173. Docker Compose leaves it unset and uses `localhost`. Sass compiles with the `sass` package already installed.

### 3. Static assets

The 13 absolute URLs are rewritten to relative paths so Vite resolves them from the CSS source
location, hashes them, and emits them under `public/build/assets/`:

- `resources/assets/pleasure-admin-panel/css/*.css`: `/build/fontawesome/fonts/` → `../fontawesome/fonts/`,
  `/build/ionicons/fonts/` → `../ionicons/fonts/`.
- `resources/assets/sass/app.scss`: `/build/css/orange.png` → `../../../node_modules/icheck/skins/flat/orange.png`.
- `resources/assets/pleasure-admin-panel/css/plugins.css`: `/build/css/chosen-sprite.png` → `../../../../node_modules/chosen-js/chosen-sprite.png`.

`../fonts/`, icheck's own skin PNGs and chosen's `chosen-sprite.png` already resolve relative to their
source files. Every `mix.copy` call is deleted.

### 4. Blade

| File | Change |
|---|---|
| `common/header/header.blade.php:13-14` | `@vite(['resources/assets/css/vendors.css', 'resources/assets/sass/app.scss'])` |
| `common/footer.blade.php:1-5` | `@vite('resources/assets/js/app.js')` |
| `layouts/auth.blade.php:5` | `@vite('resources/assets/sass/auth.scss')` |
| `layouts/auth.blade.php:42` | `@vite('resources/assets/js/auth.js')` |
| `mentors/forms/create_edit.blade.php:601`, `mentees/forms/create_edit.blade.php:520` | `@vite('resources/assets/js/iframe-contentWindow.js')` |
| 17 views with inline `<script>` | `<script>` / `<script type="text/javascript">` → `<script type="module">` |

The 17 views: `auth/login`, `common/footer`, `companies/forms/create_edit`, `companies/list`,
`home/dashboard`, `mentees/forms/create_edit`, `mentees/list_all`, `mentees/profile`,
`mentors/forms/create_edit`, `mentors/list_all`, `mentors/profile`, `mentorship_session/list_all`,
`ratings/rating`, `reports/index`, `users/forms/create_edit`, `users/list`, `users/profile`.
(`mentors/forms/create_edit_old` is excluded; it is dead.) Module scripts run in document order after
parsing, so every inline block runs after `app.js` has set up the globals.

### 5. Tooling

`package.json`:

- Add `vite@^8`, `laravel-vite-plugin@^3` to `devDependencies`.
- Remove `laravel-mix`, `webpack`, `sass-loader`, `resolve-url-loader`, `postcss`, `vue`,
  `vue-resource`, `modernizr`, `npm-modernizr`.
- Scripts: `dev: vite`, `build: vite build`, `watch: vite build --watch`, `prod: vite build`
  (kept one release as an alias so deploy scripts do not break; remove afterwards).

Other files:

- `.env.example`: `MIX_SENTRY_DSN_PUBLIC` → `VITE_SENTRY_DSN_PUBLIC`.
- `.gitignore`: add `/public/hot`; remove `/public/js`, `/public/css`, `public/fonts`,
  `public/mix-manifest.json`. Keep `/public/build`.
- `.ddev/config.yaml`: `web_extra_exposed_ports: [{ name: vite, container_port: 5173, http_port: 5172, https_port: 5173 }]`
  and `web_environment: [VITE_HMR_HOST=mentorship-matching-backend.ddev.site]`.
- `docker-compose.yml`: no change; `5173:5173` exists.
- `.github/workflows/ci.yml:37`: `npm run prod` → `npm run build`. Delete the laravel-mix comment above it.
- `README.md`, `CLAUDE.md`: replace Mix commands, `public/mix-manifest.json` → `public/build/manifest.json`,
  `MixManifestNotFoundException` → `ViteManifestNotFoundException`. Add a note that old `public/js`,
  `public/css`, `public/fonts` directories can be deleted locally.
- Delete `webpack.mix.js`.

### 6. Verification

Order matters. Step 1 happens before any Vite change so the baseline is known.

1. **Regression gate.** Extend `assertPageHealthy` in `e2e/tests/helpers.ts` to fail on any `pageerror`
   event and on any `console.error` message captured since navigation. Run `cd e2e && npm test` on
   master and record the result. If master already has JS errors, list them so they are not blamed on
   the migration.
2. `npm run build` succeeds and `public/build/manifest.json` lists the 6 entries.
3. `php artisan view:clear`, then `cd e2e && npm test` against the Docker stack. Zero new failures.
4. Manual smoke test on the Docker stack: open a modal (users list), switch tabs (mentor profile), open
   a daterangepicker and a bootstrap-select (mentor create form), toggle an iCheck box (login), open
   `/mentor/create?public=1` inside an iframe and confirm the resizer resizes.
5. `npm run dev` under Docker Compose and under DDEV: the page loads assets from port 5173 and a Sass
   edit hot-reloads.
6. `vendor/bin/phpunit` still passes (nothing server-side changes, but run it).

### 7. Rollout

Branch `feat/vite`, one PR. Mix and Vite do not coexist; the branch deletes Mix in its last commit.
Merge blocks on the `npm` CI job (now `npm run build`) and on a green e2e run reported in the PR.

## Findings during implementation

- **ES modules are strict mode; Mix's concatenated scripts were not.** `icheck/icheck.min.js` assigns
  `_determinate` without `var` (a minifier defect; `icheck.js` declares it). In strict mode that throws at
  load, aborts the whole `app.js` module graph, and every later symptom (`iCheck is not a function`,
  `SearchController is not defined`) follows. Fix: import the unminified `icheck/icheck.js`; Vite minifies
  it anyway.
- **Vite hoists CSS `@import` to the top of the bundle.** `admin1.css:40` imported
  `http://fonts.googleapis.com/...RobotoDraft` mid-file. Browsers ignore mid-file `@import`, so under Mix
  the font never loaded. Hoisted, the request is made and blocked as mixed content. The line is removed to
  keep today's rendering (RobotoDraft was never applied).
- **Rolldown has no AMD support.** select2 and DataTables use UMD wrappers that register the plugin in
  the AMD branch. webpack took that branch; Rolldown falls through to CommonJS, where both export a
  *registration function* and register nothing on load. `app.js` now calls `select2(window, window.jQuery)`
  and `dataTables(window, window.jQuery)` explicitly. Symptom before the fix: `$(...).select2 is not a function`.
- **Vite follows `jsnext:main`; webpack did not.** moment's package.json points it at an ESM build, so the
  CommonJS `require('moment')` inside bootstrap-daterangepicker received a module namespace
  (`moment is not a function`). A regex alias resolves bare `moment` to `moment/moment.js`.
- `textarea-autosize` has an `exports` map that only exposes the package root; import it bare.
- Vite 8 minifies CSS with LightningCSS, which rejects IE `*property` hacks in the theme and DataTables CSS.
  `css.lightningcss.errorRecovery: true` strips them, which is what browsers do anyway.
- The config file is `vite.config.mjs`, so Vite loads it as ESM without adding `"type": "module"`.

## Risks

- **A vendor UMD file that Mix loaded as a raw script may behave differently as an ESM import.**
  Candidates: `fastclick`, `textarea-autosize`, `sweetalert`. Mitigation: the ordered import list mirrors
  Mix exactly, and the e2e gate catches load-time errors on every page.
- **`ignore-scripts=true` and native binaries.** Vite 8 and Rolldown use optional dependencies, so
  `npm ci` installs them without scripts. If a host lacks a matching binary, the build fails loudly at
  `npm run build`, not at runtime.
- **`type="module"` changes inline script timing.** Scripts that today run during parsing now run after
  it. Every inline block is already wrapped in `$(document).ready` or an IIFE that only touches
  controllers, so nothing depends on running early. The e2e gate checks this on every page.
- **Deploy scripts outside this repo call `npm run prod`.** The alias covers one release.
