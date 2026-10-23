# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

This is a Laravel 13 (PHP >=8.3) backoffice application, internally named **JobPairs**, that manages the
matching process between mentors and mentees based on preferences and skills. It is a legacy,
server-rendered (Blade + jQuery) application — there is no SPA framework and no JSON API layer.

## Commands

### Environment

Local development runs on **DDEV** only (`.ddev/config.yaml`, committed) — PHP 8.4, Node 24, MariaDB
11.8, docroot `public`, served at `https://mentorship-matching-backend.ddev.site`, mail captured by
Mailpit. `ddev start` rewrites the DB and mail settings in `.env` to match its own services, and its
post-start hook runs `composer setup` (dependencies, `APP_KEY` if empty, frontend build), clears the
caches, runs pending migrations on the `db` database and creates the `testing` database. Ask before
`ddev start`/`restart`: the hook migrates the developer's database. Run every command through DDEV:

```bash
ddev exec php artisan migrate
ddev composer install
ddev npm run build
```

All `php artisan`, `composer`, and `npm` commands below are meant to be run inside DDEV.

The app reads `public/build/manifest.json`, which is gitignored and built at deploy time — without
`npm run build` every page fails with `ViteManifestNotFoundException`.

### PHP / Laravel

```bash
composer install                 # install PHP dependencies
php artisan migrate               # run DB migrations
php artisan db:seed               # seed lookup/reference data (roles, statuses, specialties, etc.)
```

### Tests

```bash
vendor/bin/phpunit                          # run the full suite
vendor/bin/phpunit --filter testMethodName  # run a single test
vendor/bin/phpunit tests/ExampleTest.php    # run a single test file
```

`phpunit.xml` points the test suite at `./tests` and sets (as `<server>` lines) `APP_ENV=testing`,
`DB_CONNECTION=mysql`, `DB_DATABASE=testing`, array cache/session/mail and an empty Sentry DSN. The
suite runs on a dedicated MariaDB database `testing` (the DDEV post-start hook creates it; CI names its
service database the same). Feature tests use `RefreshDatabase`, and `TestCase::$seed = true` seeds the
lookup tables. `TestCase` refuses any database except `testing` and refuses a cached config — never
weaken these guards, because `RefreshDatabase` wipes the database it runs on. Run it with
`ddev exec env COLUMNS=120 vendor/bin/phpunit`. SQLite does not work: migration
`2017_03_22_152036` drops a primary key column, and the filter SQL is MySQL-only. It covers:

- `tests/Unit/` — no fixtures: the session status id contract and its active/completed/cancelled buckets, and
  the test-database and config-cache guards (`*GuardTest`, which boot the app and must stay in the suite), and
  `ScheduleTest` (the scheduled commands; no queue worker).
- `tests/Feature/MentorshipSessionLifecycleTest` — characterization tests for the session state machine:
  creation paths, the accept/decline email-link flows, cancellation side effects on mentor/mentee
  availability, the fourth-meeting → evaluation auto-advance, and which notification goes to whom.
- `tests/Feature/UserAccessManagerTest` — the role checks behind every route middleware, including the
  documented cache-staleness behavior.
- `tests/Feature/FilterQueryTest` — the hand-written filter SQL: every filter combination, join/where
  binding order, apostrophe handling, and SQL injection payloads (formerly `tests/manual/verify_filters.php`).
- `tests/Feature/RouteAccessTest` — who may reach each route, read from the router: the explicit list of public
  routes (a new public route must be added there on purpose), guest → login for every `auth` route, and the role
  matrix of every middleware group.
- Shared fixtures live in `tests/Support/CreatesMatchingFixtures.php` (classmapped via `autoload-dev`).

The browser suite is the other one to run after touching routes or views:

```bash
cd e2e && npm test                    # Playwright: opens every route in a browser, registers a
                                      # mentor/mentee/matcher, clicks every menu option per role
```

The Playwright suite needs `npm run seed` first (the seeder also removes what previous e2e runs
registered) and honours `E2E_BASE_URL` (default `https://mentorship-matching-backend.ddev.site`); see `e2e/README.md`.

### Frontend assets

```bash
npm ci          # exact lockfile install (.npmrc: ignore-scripts, engine-strict, min-release-age=7)
npm run dev     # Vite dev server with HMR on port 5173
npm run watch   # rebuild on change without the dev server
npm run build   # production build
```

Asset pipeline is Vite (`vite.config.mjs`, `laravel-vite-plugin`). Entries: `resources/assets/js/app.js`
(one ordered list of vendor and first-party imports — `jquery-global.js` puts jQuery on `window` first,
then Bootstrap 3, Select2, DataTables, Chosen, bootstrap-select, icheck, the theme scripts, and the
page controllers), `auth.js`, `iframe-contentWindow.js`, `resources/assets/css/vendors.css`, and the
two Sass files under `resources/assets/sass`. Blade loads them with `@vite`; inline Blade scripts are
`type="module"` so they run after the bundles. There is no Vue/React app — this is jQuery-driven,
page-scoped JS, not a component framework.

Bootstrap stays at 3.4.1 (no patched 3.x release; `npm audit` reports one moderate finding). Its advisories
affect the button `data-loading-text` attribute and the tooltip/popover plugins, which no view uses today. The
plugins are still bound to `[data-toggle="tooltip"|"popover"]` and `.demo-btn-loading` (`pleasure.js`) and to
`[id^=tooltip-]` (`app.js`). Never put user data in the `title`, `data-*` or `data-loading-text` attributes of
elements that match these selectors. See README "Known npm advisories".

### Artisan commands specific to this app

- `mentees:notify-unmatched` (`HandleUnmatchedMentees`) — runs daily via the scheduler.
- `email:follow-up` (`SendFollowUpEmails`) — runs daily via the scheduler.
- `AnonymizeMentorAndMenteeEmails`, `FixDuplicates` — one-off data-maintenance commands.
- `cv:move-to-private` (`MoveCvFilesToPrivate`) — one-off upgrade step: moves CVs from the old public folder to
  private storage. Safe to rerun.
- Every class in `app/Notifications` is `ShouldQueue` (only the framework password reset mail is sent at once). On servers Supervisor runs `queue:work` and `schedule:work`
  (`DEPLOYMENT.md`); the scheduler starts no worker (`tests/Unit/ScheduleTest`). Locally `QUEUE_CONNECTION=sync`.

Deployment, the production `.env` and the upgrade notes per version live in `DEPLOYMENT.md`. A change that needs
an operator action (an `.env` key, a one-off command, a data check) adds a step to its "Unreleased" upgrade notes.

## Architecture

The codebase uses a strict three-layer backend architecture on top of Laravel's MVC. When making
changes, follow the existing layer a feature lives in rather than reaching straight from a controller
into Eloquent.

```text
Route (routes/web.php)
  → Controller (app/Http/Controllers)      — HTTP concerns, view composition, auth checks via middleware
    → Manager  (app/BusinessLogicLayer/managers) — business logic, orchestration, notifications, view models
      → Storage (app/StorageLayer)          — all Eloquent/DB query building, one class per entity
        → Model (app/Models/eloquent)       — Eloquent models, relationships
```

- **Controllers** instantiate the Managers they need directly in the constructor (`new XManager()`) —
  there is no dependency injection container usage or interface binding for these layers. Controllers
  are otherwise thin: fetch data from one or more managers, build a view array, return a Blade view.
- **Managers** (`app/BusinessLogicLayer/managers/*Manager.php`) hold the actual business rules: state
  transitions (e.g. mentorship session status changes), validation, sending notifications, caching
  role checks, converting Eloquent models into **view models** (`app/Models/viewmodels/*ViewModel.php`)
  for display. Managers often instantiate other Managers/Storages the same way controllers do.
- **Storage classes** (`app/StorageLayer/*Storage.php`) are the only place Eloquent queries should be
  written — one storage class per entity, named `<Entity>Storage`. `RawQueryStorage` holds hand-written
  SQL for cases the query builder doesn't handle well (used for filter/search screens).
- **Models** (`app/Models/eloquent`) are plain Eloquent models with relationships; naming doesn't
  always match table names 1:1 (e.g. `MentorProfile`/`MenteeProfile` models back `mentor_profile`/
  `mentee_profile` tables, distinct from the `User` model/`users` table for backoffice accounts).

### Authorization model

There is no use of Laravel Policies/Gates. Role checks live in `UserAccessManager`
(`app/BusinessLogicLayer/managers/UserAccessManager.php`), which hardcodes three role IDs
(`ADMINISTRATOR_ROLE_ID = 1`, `MATCHER_ROLE_ID = 2`, `ACCOUNT_MANAGER_ROLE_ID = 3`) matched against the
`Role`/`UserRole` pivot. Every check reads the user's roles (no cache), so a revoked role stops at
once; do not add a cache without invalidation on role changes. Route-level enforcement is done through named
middleware in `app/Http/Middleware` (`IsAdmin`, `IsAccountManager`, `CanEditMentorsAndMentees`,
`CanEditUserProfile`, `CanCreateMentorshipSession`, `CanUpdateMentorshipSession`, `CanInviteMentee`,
`CanOnlyChangeStatus`), each delegating its actual check to `UserAccessManager`. `routes/web.php`
groups routes by which middleware stack they need — check there first to see who can hit an endpoint.

- Every protected group starts with `'auth', 'staff'`. The `staff` group (`app/Http/Kernel.php`) is
  `EnsureActiveStaff` (active `state_id` 1 and at least one role: `User::isActiveStaff()`) and `SameOriginFrames`
  (staff pages cannot be framed; the public forms stay embeddable). A new protected group needs both.
- A denied role check redirects through the `DeniesAccess` trait: the session's previous URL or home, never the
  `Referer`.
- Middleware checks the role; the Manager checks ownership and throws. An account manager updates only sessions
  they manage (`updateMentorshipSessionAsUser`, admins any) and invites mentees only for them (`inviteMentee`);
  only the author or an admin deletes a session history item; an account manager claims only a company without
  another account manager. Exception: the own-capacity rule is the `can-edit-user-profile` middleware (admin or
  own profile), not a Manager check. Policy (Paul): all staff may read all data.
- Public surface: `throttle:public-forms` on the guest create forms (staff exempt), `throttle:30,1,email-links`
  on the email-link and rating routes. The rating routes are also `signed:relative`: the mails send
  `URL::temporarySignedRoute(..., 30 days, absolute: false)` prefixed with `APP_URL`, and the form posts back to
  the same signed URL. A new public route goes into `RouteAccessTest`'s public list.
- Uploaded CVs live on private storage (`CvFileStorage`, `storage/app/private/cv_files`) and download through the
  app only. Stored profile/company links render through `WebUrl::orNull()` (http/https only).
- `lang` from a request goes through `Controller::localeFromRequest()` (allow-list `en`, `gr`).

### Mentorship session lifecycle

`MentorshipSessionManager` and `MentorshipSessionStatus`/`app/Utils/MentorshipSessionStatuses.php`
implement a status-based state machine for a mentoring engagement (pending → introduction_sent →
available_mentee/available_mentor → started → first..fourth_meeting → evaluation_sent →
follow_up_sent, with cancellation states per role: `cancelled_mentee`/`cancelled_mentor`/
`cancelled_acc_man`). `MentorshipSessionStatuses` groups these IDs into active/completed/pending/
cancelled buckets — use those helpers instead of hardcoding status ID checks. Session status changes
trigger email notifications (`app/Notifications`) to mentors/mentees/account managers/matchers, several
of which are accepted/declined via signed-less GET links embedded in emails (see the
`accept-session*`/`decline-session*`/`accept-session-management`/`decline-session-management` routes in
`routes/web.php`) — these are unauthenticated routes gated only by matching id/email in the URL, not
Laravel auth or signed URLs. Paul decided to keep them unsigned; they are throttled. The rating links are signed
(see Authorization model).

### Key domain entities

- `MentorProfile` / `MenteeProfile` — the mentors/mentees being matched (distinct from `User`, which is
  backoffice staff: admins, matchers, account managers).
- `Company`, `Industry`, `Specialty`, `Residence`, `EducationLevel`, `University`, `Reference` —
  lookup/reference data attached to profiles, each with its own Manager + Storage + seeder.
  `MentorSpecialty`/`MenteeSpecialty`, `MentorIndustry` are pivot-style models.
  `AccountManagerCapacity` caps how many active sessions an account manager can hold.
- `MentorStatus` / `MenteeStatus` (with `*StatusHistory` audit trail models/tables) — availability
  status of a mentor/mentee (e.g. available, matched), separate from the mentorship session's own
  status.
- `MentorshipSession` / `MentorshipSessionHistory` — the mentor↔mentee pairing and its audit trail.
- `MentorRating` / `MenteeRating` — post-session feedback, handled by `RatingController`/`RatingManager`.

### Reports & exports

`ReportController` + `app/Utils/DataToCsvExportManager.php` drive CSV export of mentors/mentees/
sessions (`export/mentors`, `export/mentees`, `export/sessions` routes, admin-only), sent as file downloads.
Cells that start with a formula character are escaped (`cell()`).

### Views & i18n

Blade views live under `resources/views`, organized by entity (`mentors/`, `mentees/`,
`mentorship_session/`, `companies/`, `users/`, `reports/`, `ratings/`, `common/` for shared
header/menu/search partials). Translations exist for `en` and `gr` under `lang/` at the project
root (moved out of `resources/lang` in Laravel 9); app default locale is `en` (`config/app.php`). `app/ViewComposers/MenteeAndMentorMenuComposer.php` injects
menu-related data into views without controllers passing it explicitly.

### Error tracking

Sentry (`sentry/sentry-laravel`) is wired up via `SENTRY_LARAVEL_DSN` for both backend (PHP) and
frontend (`@sentry/browser`, `VITE_SENTRY_DSN_PUBLIC`) error reporting.
