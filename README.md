# Mentorship matching backend platform

This [Laravel 13](https://laravel.com/docs/13.x/) application is a platform backoffice that manages the matching process
between mentors with mentees, based on their
preferences and skills.

| | |
|---|---|
| PHP | 8.3+ (developed and tested on 8.4) |
| Laravel | 13.x |
| Node | 24 (see `.nvmrc`) |
| Database | MySQL 8 / MariaDB 11 |
| Assets | Vite |

## Table of Contents

- [Mentorship matching backend platform](#mentorship-matching-backend-platform)
  - [Table of Contents](#table-of-contents)
  - [First time install](#first-time-install)
    - [Commands](#commands)
  - [Setup the Database](#setup-the-database)
    - [Add seed data to DB](#add-seed-data-to-db)
  - [Compiling assets](#compiling-assets)
    - [Known npm advisories: Bootstrap 3](#known-npm-advisories-bootstrap-3)
  - [Testing](#testing)
    - [PHPUnit](#phpunit)
    - [Filter query regression check](#filter-query-regression-check)
    - [End-to-end browser tests](#end-to-end-browser-tests)
    - [Test accounts](#test-accounts)
  - [Troubleshooting](#troubleshooting)
  - [Contributing](#contributing)
  - [License](#license)
  - [Credits](#credits)

## First time install

Local development runs on [DDEV](https://ddev.readthedocs.io/en/stable/#installation) only. It reads the
committed `.ddev/config.yaml` and provisions PHP 8.4, Node 24, MariaDB 11.8 and Mailpit.

```bash
git clone git@github.com:scify/Mentorship-matching-backend.git
cd Mentorship-matching-backend
ddev start
ddev exec php artisan db:seed     # first time only: lookup tables and staff accounts
```

`ddev start` runs the post-start hooks in `.ddev/config.yaml`. They are safe to re-run:

- `composer setup` creates `.env` from `.env.example` when it is missing, installs the PHP dependencies,
  generates `APP_KEY` only when it is empty, runs `npm ci` only when `package-lock.json` changed, and builds
  the frontend.
- The hooks clear the caches and run pending migrations. They never run `migrate:fresh` and never seed.
- The hooks create the `testing` database for PHPUnit (see [PHPUnit](#phpunit)).

DDEV also rewrites the database and mail settings in `.env` to match its services. The site is served at
<https://mentorship-matching-backend.ddev.site>, and `ddev mailpit` opens the captured mail.

### Commands

Run every backend command through DDEV:

```bash
ddev exec php artisan migrate
ddev composer install
ddev npm run dev           # Vite dev server with HMR on port 5173
ddev ssh                   # shell inside the web container
```

> **Note:** DDEV uses MariaDB, while production uses MySQL. The two are compatible for everything this app
> does, but because parts of the app use hand-written SQL you may prefer to match production exactly with
> `ddev debug migrate-database mysql:8.0` (it converts the existing data; `ddev config --database` alone
> refuses to change the type of an existing database).

## Setup the Database

Laravel provides a simple yet powerful mechanism for creating the DB schema,
called [Migrations](https://laravel.com/docs/13.x/migrations).
Simply run `ddev exec php artisan migrate` to create the appropriate DB schema.

### Add seed data to DB

Run `ddev exec php artisan db:seed` in order to insert the starter data to the DB by
using [Laravel seeder](https://laravel.com/docs/13.x/seeding). This creates the lookup tables (roles,
statuses, specialties, universities) and a few staff accounts.

To rebuild a local database from scratch:

```bash
ddev snapshot --name before-reset    # optional: keep a copy
ddev exec php artisan migrate:fresh --seed
```

## Compiling assets

When in project root directory, run

```bash
ddev npm run dev      # Vite dev server with HMR (port 5173)
ddev npm run watch    # rebuild on change, no dev server
ddev npm run build    # production build
```

The app reads `public/build/manifest.json`, which is **not** committed. If you have not built the assets, every
page fails with `ViteManifestNotFoundException`. Leftover `public/js`, `public/css`, `public/fonts` and
`public/mix-manifest.json` from Laravel Mix can be deleted.

### Known npm advisories: Bootstrap 3

`npm audit` reports one moderate finding: `bootstrap` 3.4.1, the last Bootstrap 3 release, with no patched
version. The markup and the Pleasure admin theme are Bootstrap 3, so an upgrade to Bootstrap 5 rewrites every
view. The two advisories affect plugins that no page uses today:

- [GHSA-vxmc-5x29-h64v](https://github.com/advisories/GHSA-vxmc-5x29-h64v): the button plugin's
  `data-loading-text` attribute.
- [GHSA-q58r-hwc8-rm9j](https://github.com/advisories/GHSA-q58r-hwc8-rm9j): the tooltip and popover plugins.

The code still activates these plugins on three selectors: `pleasure.js` binds tooltips and popovers to
`[data-toggle="tooltip"]` and `[data-toggle="popover"]` and the loading button to `.demo-btn-loading`, and
`app.js` binds a tooltip to `[id^=tooltip-]`. Do not put user data in the `title`, `data-*` or
`data-loading-text` attributes of elements that match these selectors.
`bootstrap-sass` ships the same 3.4.1 JavaScript, so importing it instead only hides the warning.

## Testing

There are two suites. The PHP one runs inside DDEV; the browser suite runs on your host and talks to the
DDEV site over HTTP.

| Suite | Command | What it covers |
|---|---|---|
| PHPUnit | `ddev composer test` | The session state machine (creation, accept/decline links, cancellations, notifications), the role checks behind the route middleware, the status id contract, and the hand-written filter SQL — every filter, binding order, and SQL injection payloads. |
| End-to-end | `cd e2e && npm test` | 15 Playwright tests: every route opens, mentor/mentee/matcher register, every menu option is clicked for all three roles. Captures a screenshot per screen. |

```bash
ddev composer test     # the whole suite
ddev coverage          # with a coverage report in coverage/ (turns Xdebug on for the run only)
```

### PHPUnit

The suite runs on a dedicated MariaDB/MySQL database named `testing`, never on the development database.
`phpunit.xml` sets `DB_DATABASE=testing` (host and credentials still come from `.env`), and `TestCase`
refuses any other database and a cached config. Feature tests use `RefreshDatabase`: each run migrates
`testing` fresh and seeds the lookup tables (references, residences, statuses, ...). The DDEV post-start
hook creates the `testing` database.

```bash
ddev exec vendor/bin/phpunit --filter testMethodName        # a single test
ddev exec vendor/bin/phpunit tests/Feature/FilterQueryTest.php   # a single file
```

`tests/Feature/FilterQueryTest.php` is the filter-SQL regression check (formerly
`tests/manual/verify_filters.php`): every filter still returns the right rows, placeholder and binding
counts line up, apostrophes in names work, and injection payloads (`union select`, quote breakout,
`or 1=1`, `drop table`) are bound as values rather than parsed as SQL. Run it after any change to the
`*Manager` filter builders or to `RawQueryStorage`. `tests/Feature/MentorshipSessionLifecycleTest.php`
characterizes the session state machine — run it after touching `MentorshipSessionManager`.

### End-to-end browser tests

A [Playwright](https://playwright.dev) suite drives a real browser against the DDEV site. It writes to the
DDEV database `db`, so take a `ddev snapshot` first if you want to keep your data. A screen counts
as broken if it returns 5xx **or** renders Laravel's exception page — a 200 with a stack trace in it still
fails.

```bash
cd e2e
npm install
npx playwright install chromium   # first run only
npm run seed                      # a company plus one user per role
npm test
```

It uses `https://mentorship-matching-backend.ddev.site` by default; set `E2E_BASE_URL` to point it somewhere
else.

Useful while working on it:

```bash
npm test -- --headed          # watch it drive the browser
npm test -- 03-routes         # a single spec
npx playwright show-report    # HTML report from the last run
```

Screenshots land in `e2e/screenshots/` (gitignored), numbered in capture order, alongside a
`manifest.jsonl` recording each one's label and URL.

See [`e2e/README.md`](e2e/README.md) for what each spec asserts.

### Test accounts

`npm run seed` (or `ddev exec php e2e/fixtures/seed.php`) creates one account per role, all with the password
`password123`. They are local fixtures — do not create them anywhere public.

| Email | Role |
|---|---|
| `admin@jobpairs.test` | Administrator |
| `matcher@jobpairs.test` | Matcher |
| `accman@jobpairs.test` | Account Manager |

The login form field is `email_address`, not `email` — the app overrides Laravel's default in
`app/Interfaces/CustomAuthentication.php`, which trips people up when scripting a login.

Seeding is idempotent, so re-running it is also how you reset a password you have changed.

Separately, `php artisan db:seed` creates the staff accounts in
`database/seeders/UserTableSeeder.php` with a hardcoded default password. That is fine for a local
database; make sure those accounts do not exist with that password anywhere public.

## Troubleshooting

**`ViteManifestNotFoundException`** — the frontend assets have not been built. Run `ddev composer setup` (or `ddev npm run build`).

**`fopen(...storage/framework/cache/...): Failed to open stream: No such file or directory`** — the cache
directory is not writable by the user running PHP, and Laravel reports the failed `mkdir` as a missing file.
This usually happens after PHP ran as a different user (for example an old Docker setup). Clear the stale
files from inside the container and let them be recreated:

```bash
ddev exec sudo rm -rf storage/framework/cache/* storage/framework/views/* storage/framework/sessions/*
ddev exec php artisan optimize:clear
```

**Registration fails with "An error occurred. Please try again."** — the app sends a notification on signup,
so it needs a reachable mail server. In DDEV, `MAIL_HOST` must be `127.0.0.1` and `MAIL_PORT` `1025`
(Mailpit); `ddev restart` writes these values into `.env`.

**Role changes do not take effect** — role checks are cached without invalidation. Run `ddev exec php artisan cache:clear`.

**Before deploying**, confirm `APP_DEBUG=false` in the production `.env`. `.env.example` ships `true`, which
would expose stack traces and configuration to visitors.

## Contributing

To contribute to this project, follow these steps:

1. Fork this repository.
2. Read the [CONTRIBUTING](CONTRIBUTING.md) file.
3. Create a branch: `git checkout -b <branch_name>`.
4. Make your changes and commit them: `git commit -m '<commit_message>'`
5. Push to the original branch: `git push origin <project_name>/<location>`
6. Create the pull request.

## License

This project is open-sourced software licensed under
the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).

## Credits

Icons used in this project are made by

- [Freepik](http://www.flaticon.com/authors/freepik) from [www.flaticon.com](http://www.flaticon.com)
- [Vectors Market](http://www.flaticon.com/authors/vectors-market) from [www.flaticon.com](http://www.flaticon.com)
- [Prosymbols](http://www.flaticon.com/authors/prosymbols)  from [www.flaticon.com](http://www.flaticon.com)
