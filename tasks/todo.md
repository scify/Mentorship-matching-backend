# Vite Migration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace Laravel Mix/webpack with Vite 8 + `laravel-vite-plugin`, keeping every page's behaviour identical.

**Architecture:** One ordered ESM entry per layout (`app.js`, `auth.js`, `iframe-contentWindow.js`) plus three CSS entries. A `jquery-global.js` module puts jQuery on `window` before any plugin loads. Inline Blade scripts become `type="module"` so they run after the bundles. Theme CSS asset URLs become relative so Vite emits them.

**Tech Stack:** Vite ^8, laravel-vite-plugin ^3, sass (already installed), Laravel 13 `@vite` directive, Playwright e2e suite as the regression gate.

**Spec:** `tasks/vite-migration-design.md`

## Global Constraints

- Node 24 (`.nvmrc`), `.npmrc` has `engine-strict=true`, `ignore-scripts=true`, `legacy-peer-deps=true`. Do not change `.npmrc`.
- No behaviour change. Bootstrap 3 stays. Controllers keep `window.X = function` shape.
- Vendor imports use explicit file paths, in the same order as `webpack.mix.js` today.
- Commit as Paul. No Claude attribution lines. No "Test plan" section in the PR.
- Local stack is DDEV: `ddev exec ...`, site `https://mentorship-matching-backend.ddev.site`.
- `resources/views/mentors/forms/create_edit_old.blade.php` is dead; do not touch it.

---

### Task 0: Branch and record the plan

**Files:**
- Create: `tasks/vite-migration-design.md` (exists, untracked), `tasks/todo.md` (this file)

- [x] **Step 1: Create the branch**

```bash
git checkout -b feat/vite
```

- [x] **Step 2: Commit the spec and plan**

```bash
git add tasks/vite-migration-design.md tasks/todo.md
git commit -m "docs: add Vite migration design and plan"
```

---

### Task 1: e2e regression gate for JavaScript errors (baseline on Mix)

**Files:**
- Modify: `e2e/tests/helpers.ts:1` (imports), `:72-89` (`assertPageHealthy`)
- Modify: `e2e/tests/02-matcher-registration.spec.ts:1`, `e2e/tests/03-routes.spec.ts:1`, `e2e/tests/04-navigation.spec.ts:1`

**Interfaces:**
- Produces: `export const test` from `./helpers` — a Playwright `test` whose `page` fixture records `pageerror` events and `console.error` messages. `assertPageHealthy(page, status, where)` now also fails when any were recorded since the last call, and clears the list.

- [x] **Step 1: Extend the `page` fixture in `helpers.ts`**

Replace line 1 `import { expect, Page } from '@playwright/test';` with:

```ts
import { expect, Page, test as base } from '@playwright/test';

/**
 * JavaScript errors per page, collected from the moment the page is created.
 * Registering the listeners inside the fixture (not inside a helper called
 * after `goto`) is what makes load-time errors such as `$ is not defined`
 * visible. Resource 404s are console errors too, but they are not JS
 * failures, so they are filtered out.
 */
const jsErrors = new WeakMap<Page, string[]>();

export const test = base.extend({
  page: async ({ page }, use) => {
    const errors: string[] = [];
    jsErrors.set(page, errors);
    page.on('pageerror', (err) => errors.push(`pageerror: ${err.message}`));
    page.on('console', (msg) => {
      if (msg.type() === 'error' && !msg.text().startsWith('Failed to load resource')) {
        errors.push(`console.error: ${msg.text()}`);
      }
    });
    await use(page);
  },
});
```

- [x] **Step 2: Assert on collected errors in `assertPageHealthy`**

After the `for (const marker of markers) {...}` loop (line 88), add:

```ts
  const errors = jsErrors.get(page) ?? [];
  const seen = [...errors];
  errors.length = 0;
  expect(seen, `${where} raised JavaScript errors:\n${seen.join('\n')}`).toEqual([]);
```

Update the doc comment above the function (lines 68-71) to:

```ts
/**
 * A page is "broken" if the server 5xx'd, Laravel rendered its exception
 * page, or the browser logged a JavaScript error while loading it. Checking
 * only the status code misses errors swallowed into a 200.
 */
```

- [x] **Step 3: Switch the three specs to the extended `test`**

In `02-matcher-registration.spec.ts`, `03-routes.spec.ts`, `04-navigation.spec.ts` change line 1 from
`import { expect, test } from '@playwright/test';` to `import { expect } from '@playwright/test';`
and add `test` to the existing `./helpers` import on line 2, e.g.
`import { assertPageHealthy, login, makeShotter, Role, test } from './helpers';`.

- [x] **Step 4: Install the e2e suite and seed fixtures against DDEV**

```bash
cd e2e && npm ci && npx playwright install chromium
ddev exec php e2e/fixtures/seed.php
```

- [x] **Step 5: Run the baseline on master's asset build (Mix)**

```bash
cd /home/paul/projects/Mentorship-matching-backend && npm run prod
cd e2e && E2E_BASE_URL=https://mentorship-matching-backend.ddev.site npx playwright test
```

Expected: PASS. If a page already raises a JS error on Mix, record the exact message in `tasks/todo.md`
under "Baseline findings" and do not attribute it to the migration later. If the DDEV certificate blocks
Playwright, add `ignoreHTTPSErrors: true` to `use` in `e2e/playwright.config.ts:17`.

- [x] **Step 6: Commit**

```bash
git add e2e/tests/helpers.ts e2e/tests/02-matcher-registration.spec.ts e2e/tests/03-routes.spec.ts e2e/tests/04-navigation.spec.ts e2e/playwright.config.ts
git commit -m "test(e2e): fail page checks on JavaScript errors"
```

---

### Task 2: Vite config and module entries (Vite builds alongside Mix)

**Files:**
- Create: `vite.config.mjs`, `resources/assets/js/jquery-global.js`, `resources/assets/css/vendors.css`
- Modify: `resources/assets/js/app.js` (rewrite), `resources/assets/js/auth.js` (rewrite), `resources/assets/js/iframe-contentWindow.js` (new), `resources/assets/pleasure-admin-panel/js/custom-forms-pickers.js:1`
- Modify: `resources/assets/js/controllers/{Companies,Mentees,Mentors,MentorshipSessions,Rating,Users}ListController.js:1-2`, `RatingController.js:1`
- Modify: `package.json` (add deps, do not remove yet)

**Interfaces:**
- Produces: six Vite inputs: `resources/assets/css/vendors.css`, `resources/assets/sass/app.scss`, `resources/assets/sass/auth.scss`, `resources/assets/js/app.js`, `resources/assets/js/auth.js`, `resources/assets/js/iframe-contentWindow.js`. Task 4 references these exact paths in `@vite()`.

- [x] **Step 1: Install Vite**

```bash
npm install --save-dev vite@^8 laravel-vite-plugin@^3
```

- [x] **Step 2: Create `vite.config.mjs`**

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
```

- [x] **Step 3: Create `resources/assets/js/jquery-global.js`**

```js
// Every entry imports this first. ESM evaluates imports in source order, so
// jQuery is on window before any jQuery plugin file (Bootstrap 3, icheck,
// chosen, jasny, bootstrap-select, toastr, daterangepicker) executes.
import $ from 'jquery';

window.$ = window.jQuery = $;
```

- [x] **Step 4: Create `resources/assets/css/vendors.css`** (same order as `mix.styles` in `webpack.mix.js`)

```css
@import 'select2/dist/css/select2.min.css';
@import 'jasny-bootstrap/dist/css/jasny-bootstrap.min.css';
@import 'bootstrap-select/dist/css/bootstrap-select.min.css';
@import 'datatables/media/css/jquery.dataTables.min.css';
@import 'chosen-js/chosen.css';
@import 'icheck/skins/flat/_all.css';
@import 'bootstrap-daterangepicker/daterangepicker.css';
@import '../pleasure-admin-panel/css/admin1.css';
@import '../pleasure-admin-panel/css/elements.css';
@import '../pleasure-admin-panel/css/plugins.css';
@import 'ion-rangeslider/css/ion.rangeSlider.min.css';
```

- [x] **Step 5: Rewrite `resources/assets/js/app.js`**

Replace the whole file. Import order = `libs.js` array order in `webpack.mix.js`, then the old `app.js` body, then the `controllers.js` array order.

```js
import './jquery-global';
import _ from 'lodash';
import 'jquery-validation/dist/jquery.validate.min.js';
import 'jquery-ui-dist/jquery-ui.min.js';
import 'icheck/icheck.js';  // .min assigns an undeclared `_determinate`; strict-mode ESM throws
import 'chosen-js/chosen.jquery.js';
import select2 from 'select2/dist/js/select2.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'velocity-animate/velocity.min.js';
import 'moment';
import 'toastr';
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
import Popper from 'popper.js';
import * as Sentry from '@sentry/browser';
import './controllers/CompaniesListController.js';
import './controllers/MatchingController.js';
import './controllers/MenteesListController.js';
import './controllers/MentorshipSessionsListController.js';
import './controllers/MentorsListController.js';
import './controllers/RatingController.js';
import './controllers/SearchController.js';
import './controllers/UserFormController.js';
import './controllers/UserProfileController.js';
import './controllers/UsersListController.js';

window._ = _;
window.Popper = Popper;

// select2 and DataTables ship UMD wrappers that register the plugin from their
// AMD branch. webpack honoured AMD; Vite does not, so their CommonJS export is a
// function that has to be called with the jQuery instance to register.
select2(window, window.jQuery);
dataTables(window, window.jQuery);

if (import.meta.env.VITE_SENTRY_DSN_PUBLIC) {
    Sentry.init({
        dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
    });
}

$(document).ready(function () {
    console.log('Document ready');
    Pleasure.init();
    Layout.init();
    // initialize pickers
    CustomFormsPickers.init();
    $("[id^=tooltip-]").tooltip();
    setTimeout(function () {
        /*Close any flash message after some time*/
        $(".alert-dismissable").fadeTo(4000, 500).slideUp(500, function () {
            $(".alert-dismissable").alert('close');
        });
    }, 5000);

    // initialize iCheck
    $("input[type='checkbox'], input[type='radio']").iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass: 'iradio_flat-orange'
    });
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

- [x] **Step 6: Create `resources/assets/js/auth.js` and `resources/assets/js/iframe-contentWindow.js`**

`auth.js`:
```js
import './jquery-global';
import './AuthPage.js';
```

`iframe-contentWindow.js`:
```js
import 'iframe-resizer/js/iframeResizer.contentWindow.min.js';
```

- [x] **Step 7: Convert `require()` to `import` in first-party files**

In each of `CompaniesListController.js`, `MenteesListController.js`, `MentorsListController.js`,
`MentorshipSessionsListController.js`, `UsersListController.js`, replace the first two lines
```js
const {Pleasure} = require("../../pleasure-admin-panel/js/pleasure");
const toastr = require('toastr');
```
with
```js
import { Pleasure } from "../../pleasure-admin-panel/js/pleasure";
import toastr from 'toastr';
```
(`MentorsListController.js` has only the `Pleasure` line.) In `RatingController.js` line 1:
`const toastr = require('toastr');` → `import toastr from 'toastr';`.

`resources/assets/pleasure-admin-panel/js/custom-forms-pickers.js` already has `import moment from "moment";` on line 1; leave it.

In `resources/assets/pleasure-admin-panel/js/pleasure.js` lines 3-7 replace
```js
require('bootstrap');
require('bootstrap-select');
require('bootstrap-select');
require('fastclick');
require('velocity-animate');
```
with
```js
import 'bootstrap';
import 'bootstrap-select';
import 'fastclick';
import 'velocity-animate';
```
(the duplicate `bootstrap-select` line is dropped). In `resources/assets/pleasure-admin-panel/js/layout.js` line 1:
`const {Pleasure} = require("./pleasure");` → `import { Pleasure } from "./pleasure";`.

- [x] **Step 8: Verify no `require(` remains and the Vite build succeeds**

```bash
grep -rn "require(" resources/assets/js resources/assets/pleasure-admin-panel/js || echo "clean"
npx vite build 2>&1 | tail -25
ls public/build/manifest.json && node -e "const m=require('./public/build/manifest.json'); console.log(Object.keys(m).filter(k=>m[k].isEntry))"
```

Expected: `clean`; build succeeds; the six entry keys print. Warnings about `../../globals/img/...` are
expected (broken today, see spec Non-goals). Any other warning or error: stop and investigate.

- [x] **Step 9: Commit**

```bash
git add vite.config.js package.json package-lock.json resources/assets/js resources/assets/css resources/assets/pleasure-admin-panel/js/custom-forms-pickers.js
git commit -m "build: add Vite config and ESM entry points"
```

---

### Task 3: Relative asset URLs in theme CSS and app.scss

**Files:**
- Modify: `resources/assets/pleasure-admin-panel/css/admin1.css:7974-7975, 10330-10331`
- Modify: `resources/assets/pleasure-admin-panel/css/plugins.css:93`
- Modify: `resources/assets/sass/app.scss:1050`

- [x] **Step 1: Rewrite the 13 absolute URLs**

```bash
sed -i 's#url(/build/fontawesome/fonts/#url(../fontawesome/fonts/#g; s#url(/build/ionicons/fonts/#url(../ionicons/fonts/#g' resources/assets/pleasure-admin-panel/css/admin1.css
sed -i 's#url(/build/css/chosen-sprite.png)#url(../../../../node_modules/chosen-js/chosen-sprite.png)#' resources/assets/pleasure-admin-panel/css/plugins.css
sed -i 's#url(/build/css/orange.png)#url(../../../node_modules/icheck/skins/flat/orange.png)#' resources/assets/sass/app.scss
grep -rn "url(/build/" resources/assets || echo "no absolute /build/ urls left"
```

- [x] **Step 2: Verify the fonts and images are emitted**

```bash
npx vite build 2>&1 | grep -iE "error|fontawesome-webfont|ionicons\.(woff|ttf)|orange|chosen-sprite" | head
ls public/build/assets | grep -cE "fontawesome-webfont|ionicons|orange|chosen-sprite"
```

Expected: no `error` line; at least 9 matching files (5 fontawesome formats, 4 ionicons formats, orange.png, chosen-sprite.png).

- [x] **Step 2b: Drop the mid-file Google Fonts import**

`admin1.css:40` is `@import url("http://fonts.googleapis.com/css?family=RobotoDraft:300,400,500");`. Delete the
line. Browsers ignored it mid-file under Mix; Vite hoists it and the browser blocks it as mixed content.

- [x] **Step 3: Commit**

```bash
git add resources/assets/pleasure-admin-panel/css/admin1.css resources/assets/pleasure-admin-panel/css/plugins.css resources/assets/sass/app.scss
git commit -m "style: reference theme fonts and sprites relatively for Vite"
```

---

### Task 4: Blade switches to `@vite` and module inline scripts

**Files:**
- Modify: `resources/views/common/header/header.blade.php:13-14`
- Modify: `resources/views/common/footer.blade.php:1-6`
- Modify: `resources/views/layouts/auth.blade.php:5, 42`
- Modify: `resources/views/mentors/forms/create_edit.blade.php:601, 603`, `resources/views/mentees/forms/create_edit.blade.php:520, 522`
- Modify: the 15 other views listed in Step 3

- [x] **Step 1: Replace the `mix()` calls**

`header.blade.php` lines 13-14 →
```blade
    @vite(['resources/assets/css/vendors.css', 'resources/assets/sass/app.scss'])
```
`footer.blade.php` lines 1-5 →
```blade
@vite('resources/assets/js/app.js')
```
`auth.blade.php` line 5 → `@vite('resources/assets/sass/auth.scss')`; line 42 → `@vite('resources/assets/js/auth.js')`.
`mentors/forms/create_edit.blade.php:601` and `mentees/forms/create_edit.blade.php:520` →
`@vite('resources/assets/js/iframe-contentWindow.js')` (keep surrounding indentation and any `@if`).

- [x] **Step 2: Verify no `mix(` remains**

```bash
grep -rn "mix(" resources/views || echo "clean"
```

- [x] **Step 3: Add `type="module"` to the 17 inline scripts**

```bash
for f in \
  resources/views/auth/login.blade.php \
  resources/views/common/footer.blade.php \
  resources/views/companies/forms/create_edit.blade.php \
  resources/views/companies/list.blade.php \
  resources/views/home/dashboard.blade.php \
  resources/views/mentees/forms/create_edit.blade.php \
  resources/views/mentees/list_all.blade.php \
  resources/views/mentees/profile.blade.php \
  resources/views/mentors/forms/create_edit.blade.php \
  resources/views/mentors/list_all.blade.php \
  resources/views/mentors/profile.blade.php \
  resources/views/mentorship_session/list_all.blade.php \
  resources/views/ratings/rating.blade.php \
  resources/views/reports/index.blade.php \
  resources/views/users/forms/create_edit.blade.php \
  resources/views/users/list.blade.php \
  resources/views/users/profile.blade.php; do
  sed -i -E 's#<script\s*>#<script type="module">#' "$f"
done
grep -rnE "<script\s*>" resources/views | grep -v create_edit_old || echo "all inline scripts are modules"
```

- [x] **Step 4: Build, clear compiled views, rename the local env var, run the gate**

```bash
npx vite build 2>&1 | tail -3
ddev exec php artisan view:clear
sed -i 's/^MIX_SENTRY_DSN_PUBLIC=/VITE_SENTRY_DSN_PUBLIC=/' .env
cd e2e && E2E_BASE_URL=https://mentorship-matching-backend.ddev.site npx playwright test
```

Expected: PASS with zero new failures compared to the Task 1 baseline. A `$ is not defined` or
`X is not a constructor` failure means an inline script or import order is wrong: fix it, do not skip.

- [x] **Step 5: Commit**

```bash
git add resources/views
git commit -m "feat(views): load assets through @vite and defer inline scripts"
```

---

### Task 5: Remove Laravel Mix and update tooling

**Files:**
- Delete: `webpack.mix.js`
- Modify: `package.json` (scripts, devDependencies, dependencies), `.gitignore:11-12, 19, 22`, `.env.example:43`, `.ddev/config.yaml:14`, `.github/workflows/ci.yml:34-37`

- [x] **Step 1: Remove Mix and unused packages**

```bash
npm uninstall laravel-mix webpack sass-loader resolve-url-loader postcss vue vue-resource modernizr npm-modernizr
git rm webpack.mix.js
```

- [x] **Step 2: Replace the scripts block in `package.json`**

```json
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "watch": "vite build --watch",
    "prod": "vite build"
  },
```
(`prod` is an alias for one release so external deploy scripts keep working.)

- [x] **Step 3: `.gitignore`**

Remove the lines `/public/js`, `/public/css`, `public/fonts`, `public/mix-manifest.json`. Add `/public/hot`
directly under `/public/build`.

- [x] **Step 4: `.env.example:43`**

`MIX_SENTRY_DSN_PUBLIC="${SENTRY_LARAVEL_DSN}"` → `VITE_SENTRY_DSN_PUBLIC="${SENTRY_LARAVEL_DSN}"`

- [x] **Step 5: `.ddev/config.yaml`**

Replace line 14 `web_environment: []` with:
```yaml
web_environment:
    - VITE_HMR_HOST=mentorship-matching-backend.ddev.site
web_extra_exposed_ports:
    - name: vite
      container_port: 5173
      http_port: 5172
      https_port: 5173
```

- [x] **Step 6: `.github/workflows/ci.yml:34-37`**

Replace
```yaml
            # laravel-mix 6 depends on webpack internals; a webpack bump can pass
            # npm ci and still break the build. Assets are built at deploy time.
            - name: Build assets
              run: npm run prod
```
with
```yaml
            # Assets are built at deploy time; this catches a build that npm ci
            # alone would not.
            - name: Build assets
              run: npm run build
```

- [x] **Step 7: Verify a clean install builds**

```bash
rm -rf node_modules && npm ci && npm run build 2>&1 | tail -3
grep -cE "laravel-mix|webpack|\"vue\"|modernizr" package.json || echo "no Mix leftovers in package.json"
```

Expected: build succeeds; `0` / "no Mix leftovers".

- [x] **Step 8: Commit**

```bash
git add package.json package-lock.json .gitignore .env.example .ddev/config.yaml .github/workflows/ci.yml
git commit -m "build: remove Laravel Mix, wire Vite into CI and DDEV"
```

---

### Task 6: Documentation

**Files:**
- Modify: `README.md:59, 78, 101, 205-211, 310`, `CLAUDE.md:24, 39-40, 89-94`, `e2e/README.md:26`

- [x] **Step 1: README.md**

- Line 59: `ddev npm run prod` → `ddev npm run build`.
- Line 78: `ddev npm run watch` → `ddev npm run dev          # Vite dev server with HMR on port 5173`.
- Line 101: `npm install && npm run prod` → `npm install && npm run build`.
- Lines 205-207 →
  ```
  npm run dev      # Vite dev server with HMR (port 5173)
  npm run watch    # rebuild on change, no dev server
  npm run build    # production build
  ```
- Lines 210-211 → `The app reads \`public/build/manifest.json\`, which is **not** committed. If you have not built the assets, every page fails with \`ViteManifestNotFoundException\`. Old \`public/js\`, \`public/css\`, \`public/fonts\` and \`public/mix-manifest.json\` from Laravel Mix can be deleted.`
- Line 310: `**\`MixManifestNotFoundException\`**` → `**\`ViteManifestNotFoundException\`**`, and `npm run prod` → `npm run build`.

- [x] **Step 2: CLAUDE.md**

- Line 24: `ddev npm run prod` → `ddev npm run build`.
- Lines 39-40 → `The app reads \`public/build/manifest.json\`, which is gitignored and built at deploy time — without \`npm run build\` every page fails with \`ViteManifestNotFoundException\`.`
- Lines 89-91 →
  ```
  npm run dev     # Vite dev server with HMR on port 5173
  npm run watch   # rebuild on change without the dev server
  npm run build   # production build
  ```
- Line 94 onward: rewrite the paragraph to: `Asset pipeline is Vite (\`vite.config.js\`, \`laravel-vite-plugin\`). Entries: \`resources/assets/js/app.js\` (one ordered list of vendor and first-party imports; \`jquery-global.js\` puts jQuery on \`window\` first), \`auth.js\`, \`iframe-contentWindow.js\`, \`resources/assets/css/vendors.css\`, and the two Sass files. Inline Blade scripts are \`type="module"\` so they run after the bundles. There is no Vue/React app — this is jQuery-driven, page-scoped JS.`

- [x] **Step 3: e2e/README.md:26** — `npm run prod` → `npm run build`.

- [x] **Step 4: Commit**

```bash
git add README.md CLAUDE.md e2e/README.md
git commit -m "docs: describe the Vite asset pipeline"
```

---

### Task 7: Final verification and PR

- [x] **Step 1: Full e2e run**

```bash
npm run build && ddev exec php artisan view:clear
cd e2e && E2E_BASE_URL=https://mentorship-matching-backend.ddev.site npx playwright test
```
Expected: PASS.

- [x] **Step 2: PHPUnit**

```bash
ddev exec vendor/bin/phpunit
```
Expected: PASS.

- [x] **Step 3: Dev server under DDEV**

```bash
ddev restart
ddev exec npm run dev   # leave running
```
Open `https://mentorship-matching-backend.ddev.site/login`. Expected: page renders; network tab shows
assets from `https://mentorship-matching-backend.ddev.site:5173/`. Edit a colour in
`resources/assets/sass/_variables.scss`; the page updates without reload. Revert the edit. Stop the server.
Then `npm run build` again so `public/hot` is removed and the built assets serve.

- [x] **Step 4: Manual smoke test (built assets)** — done as a Playwright script; results in the Review section.

Log in as `admin@jobpairs.test` / `password123`. Check: users list → open a modal; a mentor profile → switch tabs;
mentor create form → daterangepicker opens, bootstrap-select opens, iCheck toggles; `/mentor/create?public=1`
renders inside an iframe with the content-window resizer present (`window.parentIFrame` defined in console).

- [x] **Step 5: Push and open the PR**

```bash
git push -u origin feat/vite
gh pr create --title "build: replace Laravel Mix with Vite" --body-file - <<'EOF'
Replaces Laravel Mix/webpack with Vite 8 and laravel-vite-plugin.

Why: laravel-mix 6.0.49 (last release, June 2025) requires webpack internals that webpack 5.106+ removed. The build only worked with an exact webpack pin. Vite removes the pin and the unmaintained dependency.

What changed
- One ESM entry per layout (`app.js`, `auth.js`, `iframe-contentWindow.js`) and three CSS entries. `jquery-global.js` puts jQuery on `window` before any plugin loads. Import order mirrors the old `webpack.mix.js` arrays.
- Blade loads assets through `@vite`. 17 inline scripts are `type="module"` so they run after the bundles.
- Theme CSS references fonts and sprites relatively; Vite emits them. All `mix.copy` steps are gone.
- Removed: laravel-mix, webpack, sass-loader, resolve-url-loader, postcss, and the unused vue, vue-resource, modernizr, npm-modernizr.
- `npm run build` replaces `npm run prod` (kept as an alias for one release). CI, DDEV, README and CLAUDE.md updated.
- The e2e suite now fails a page check on any browser JavaScript error.

Not changed: Bootstrap 3, the jQuery controllers, `mentors/forms/create_edit_old.blade.php` (dead view).

Local: rename `MIX_SENTRY_DSN_PUBLIC` to `VITE_SENTRY_DSN_PUBLIC` in `.env`; delete `public/js`, `public/css`, `public/fonts`, `public/mix-manifest.json`.
EOF
```

- [x] **Step 6: Review section in `tasks/todo.md`**

Append a "Review" section: what was verified, baseline findings, anything left out.

## Baseline findings

Run on 2026-09-22 against the Mix build on DDEV (`E2E_BASE_URL=https://mentorship-matching-backend.ddev.site`):
17 passed, 1 failed. No page raised a JavaScript error.

- `05-exports.spec.ts:18` "sessions export downloads a CSV without erroring" — the response `content-type`
  does not contain `csv`. Server-side; unrelated to assets. Pre-existing on master.

## Review (2026-09-22)

**Verified**

- `npm ci` from a clean `node_modules` installs 62 packages (Mix stack: ~860) and `npm run build` emits the
  6 entries plus 13 hashed font/sprite files. No `laravel-mix`, `webpack`, `vue` or `modernizr` in the lock.
- e2e against the Vite build on DDEV: 17 passed, 1 failed — the same pre-existing `05-exports` sessions CSV
  failure as the Mix baseline. Zero JavaScript errors on any page, as enforced by the new gate.
- PHPUnit inside DDEV: 35 tests, 143 assertions, OK.
- Dev server under DDEV: hot file `https://mentorship-matching-backend.ddev.site:5173`, CORS header for the
  page origin, 9 asset tags served from `:5173`, public routes pass in Chromium, `[vite] connected` logged.
- Scripted smoke on built assets (admin login): 5 iCheck boxes on `/login`; `#deleteMentorModal` opens with
  `.in` and closes; profile tab (`data-href="skills"`) activates and its pane is visible; daterangepicker
  registered and initialised with a working `moment`; 1 select2 and 6 Chosen widgets on the forms; 2 range
  sliders on `/mentors/all`; `selectpicker` registered (no view renders a `select.selecter`, so 0 widgets is
  correct); the public mentor form loads `iframe-contentWindow`. Zero page errors.

**Differences from the plan, all recorded in the spec's "Findings during implementation"**

- `icheck` is imported unminified (the `.min` build breaks strict mode).
- `select2` and DataTables are registered by an explicit call (Rolldown has no AMD).
- `moment` is aliased to `moment/moment.js` (Vite follows `jsnext:main`, webpack did not).
- `admin1.css:40` (`http://` Google Fonts import) is removed; it was ignored mid-file under Mix.
- `textarea-autosize` is imported bare (its `exports` map); `css.lightningcss.errorRecovery` is on
  (IE `*property` hacks in vendored CSS); the config is `vite.config.mjs`.
- The DDEV dev server needs `hmr.protocol: 'wss'`, `allowedHosts` and `cors` for the page origin.
- `pleasure.js` and `layout.js` also had `require()` calls (missed by the first scan); converted.

**Left out on purpose**

- `resources/views/mentors/forms/create_edit_old.blade.php` (dead) is untouched.
- The `05-exports` sessions CSV failure is pre-existing and server-side.
- `pleasure.js:350` calls `.textareaAutoSize()`, which the ESM build of `textarea-autosize` never
  registers. The caller (`initAutoSizeTextarea`) is commented out at `pleasure.js:563`, so nothing runs it.
- The smoke script lives in the session scratchpad, not the repo. Turning it into a sixth e2e spec is a
  reasonable follow-up.
