# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

This is a Laravel 8 (PHP >=7.3) backoffice application, internally named **JobPairs**, that manages the
matching process between mentors and mentees based on preferences and skills. It is a legacy,
server-rendered (Blade + jQuery) application — there is no SPA framework and no JSON API layer.

## Commands

### Environment

The project runs via Docker Compose (`docker-compose.yml`): a `php` service (app code, mounted at
`/var/www`), `nginx`, `db` (MySQL, exposed on host port `3316`), and Redis. Enter the running app
container to execute backend commands:

```bash
docker exec -it mentorship_matching_platform_server bash
```

All `php artisan`, `composer`, and `npm` commands below are meant to be run inside that container
(or in an equivalent local PHP 7.3+/Node 14 environment — see `.nvmrc`).

### PHP / Laravel

```bash
composer install                 # install PHP dependencies
php artisan migrate               # run DB migrations
php artisan db:seed               # seed lookup/reference data (roles, statuses, specialties, etc.)
php artisan serve                 # run local dev server
```

### Tests

```bash
vendor/bin/phpunit                          # run the full suite
vendor/bin/phpunit --filter testMethodName  # run a single test
vendor/bin/phpunit tests/ExampleTest.php    # run a single test file
```

`phpunit.xml` points the test suite at `./tests` and forces `APP_ENV=testing`, `CACHE_DRIVER=array`,
`SESSION_DRIVER=array`, `QUEUE_DRIVER=sync`. In practice the `tests/` directory only contains the
default Laravel scaffold (`ExampleTest.php`, `TestCase.php`) — there is effectively no real automated
test coverage for the application's business logic. Don't assume behavior is protected by tests;
verify manually (e.g. via `php artisan tinker`, seeded data, or exercising the relevant controller
route) before and after changes to the manager/storage layers.

### Frontend assets

```bash
npm install
npm run dev     # compile assets for development (Laravel Mix / webpack 5)
npm run watch   # recompile on change
npm run prod    # production build
```

Asset pipeline is Laravel Mix (`webpack.mix.js`), compiling Sass (`resources/assets/sass`) and a large
set of vendored JS libraries (jQuery, Select2, DataTables, Chosen, bootstrap-select, icheck, etc.)
plus first-party page scripts under `resources/assets/js`. There is no Vue/React app despite `vue`
being present in `package.json` — this is jQuery-driven, page-scoped JS, not a component framework.

### Artisan commands specific to this app

- `mentees:notify-unmatched` (`HandleUnmatchedMentees`) — runs daily via the scheduler.
- `email:follow-up` (`SendFollowUpEmails`) — runs daily via the scheduler.
- `AnonymizeMentorAndMenteeEmails`, `FixDuplicates` — one-off data-maintenance commands.
- The scheduler (`app/Console/Kernel.php`) also runs `queue:work --stop-when-empty` every two minutes
  (queue driver is DB/sync depending on env — there's no persistent queue worker process).

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
`Role`/`UserRole` pivot, with per-check results cached via the `Cache` facade keyed by
`<role_key><user_id>` (no TTL/invalidation beyond default cache behavior — be aware role changes may
not reflect immediately without clearing cache). Route-level enforcement is done through named
middleware in `app/Http/Middleware` (`IsAdmin`, `IsAccountManager`, `CanEditMentorsAndMentees`,
`CanEditUserProfile`, `CanCreateMentorshipSession`, `CanUpdateMentorshipSession`, `CanInviteMentee`,
`CanOnlyChangeStatus`), each delegating its actual check to `UserAccessManager`. `routes/web.php`
groups routes by which middleware stack they need — check there first to see who can hit an endpoint.

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
Laravel auth or signed URLs.

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
sessions (`export/mentors`, `export/mentees`, `export/sessions` routes, admin-only). Export file paths
for reference CSVs consumed elsewhere are configured via `MENTORS_EXCEL_FILE_PATH`/
`MENTEES_EXCEL_FILE_PATH` env vars.

### Views & i18n

Blade views live under `resources/views`, organized by entity (`mentors/`, `mentees/`,
`mentorship_session/`, `companies/`, `users/`, `reports/`, `ratings/`, `common/` for shared
header/menu/search partials). Translations exist for `en` and `gr` under `resources/lang`; app default
locale is `en` (`config/app.php`). `app/ViewComposers/MenteeAndMentorMenuComposer.php` injects
menu-related data into views without controllers passing it explicitly.

### Error tracking

Sentry (`sentry/sentry-laravel`) is wired up via `SENTRY_LARAVEL_DSN` for both backend (PHP) and
frontend (`@sentry/browser`, `MIX_SENTRY_DSN_PUBLIC`) error reporting.
