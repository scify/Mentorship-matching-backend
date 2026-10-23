# Mentorship matching backend platform

[![CI](https://github.com/scify/Mentorship-matching-backend/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/scify/Mentorship-matching-backend/actions/workflows/ci.yml)
[![Security](https://github.com/scify/Mentorship-matching-backend/actions/workflows/security.yml/badge.svg)](https://github.com/scify/Mentorship-matching-backend/actions/workflows/security.yml)
[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](LICENCE.md)

JobPairs is the backoffice that manages the matching of mentors with mentees, based on their preferences and
skills. Mentors and mentees register through public forms. Matchers pair them in mentorship sessions, account managers
follow each session until the evaluation, and administrators manage the staff accounts and the companies and
export reports.

## Contents

- [Tech stack](#tech-stack)
- [Requirements](#requirements)
- [Getting started](#getting-started)
- [Tests](#tests)
- [Code quality](#code-quality)
- [Continuous integration](#continuous-integration)
- [Releasing](#releasing)
- [Troubleshooting](#troubleshooting)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Tech stack

- Laravel 13 on PHP 8.4, server-rendered Blade views, `laravel/ui` authentication
- MySQL in production; MariaDB 11.8 in DDEV and CI
- Vite 8 with jQuery 3.7, Bootstrap 3.4 and the "Pleasure" admin theme (`resources/assets`)
- Node 24 (version in `.nvmrc`), npm with supply-chain settings in `.npmrc`
- Queued mail notifications and a daily scheduler (see [DEPLOYMENT.md](DEPLOYMENT.md))
- Sentry for backend and browser errors (optional)

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

## Requirements

- [DDEV](https://ddev.readthedocs.io/en/stable/#installation) with Docker. DDEV provides PHP 8.4, MariaDB 11.8,
  Node (version from `.nvmrc`), Composer and Mailpit.
- For the browser tests only: Node on the host, with the version from `.nvmrc`.

## Getting started

1. Clone the repository and start DDEV:

   ```bash
   git clone git@github.com:scify/Mentorship-matching-backend.git
   cd Mentorship-matching-backend
   ddev start
   ```

   Every `ddev start` runs the post-start hooks in `.ddev/config.yaml`:
   - `.ddev/scripts/sync-node-version.sh` sets `nodejs_version` in `.ddev/config.yaml` to the version in `.nvmrc`.
     After a change to `.nvmrc`, run `ddev restart` to use the new version, and commit the updated
     `.ddev/config.yaml` too.
   - The hooks create `storage/` and `bootstrap/cache/` and make them writable.
   - `composer setup` creates `.env` from `.env.example` (only if `.env` is missing), installs the PHP
     dependencies, generates `APP_KEY` (only if it is empty), runs `npm ci` (only when `package-lock.json`
     changed) and builds the frontend. It is safe to run again at any time, also by hand: `ddev composer setup`.
   - The hooks clear the caches and run pending migrations on the `db` database. They never run
     `migrate:fresh` and never seed.
   - The hooks create the `testing` database for PHPUnit (see [Tests](#tests)).
   - `ddev import-db` also runs the pending migrations afterwards (`post-import-db` hook).

   DDEV writes its database and mail settings into `.env`.

2. On a new, empty database only: create the lookup data (roles, statuses, specialties, universities) and the
   staff accounts:

   ```bash
   ddev artisan db:seed
   ```

   Do not run it again on a database with data: the seeders empty their tables first, so a second run fails on a
   foreign key or deletes lookup rows, and it resets the staff passwords. To start again, rebuild the database
   with `ddev artisan migrate:fresh --seed`.

   `database/seeders/UserTableSeeder.php` creates the staff accounts with a hardcoded default password. That is
   fine for a local database; make sure those accounts do not exist with that password anywhere public.
   For one account per role with a known password, see [Test accounts](#test-accounts).

3. For frontend work, start the Vite dev server (hot reload on port 5173):

   ```bash
   ddev npm run dev
   ```

   `ddev composer setup` (and every `ddev start`) builds the assets for production with `npm run build`. The app
   reads `public/build/manifest.json`, which git does not track. `VITE_*` values in `.env` are read at build time,
   so set them before you build.

4. Open <https://mentorship-matching-backend.ddev.site>.

### Everyday commands

| Task | Command |
|---|---|
| Start or stop the environment | `ddev start`, `ddev stop` |
| Run Artisan, Composer or npm in the container | `ddev artisan ...`, `ddev composer ...`, `ddev npm ...` |
| Rebuild the assets on every change, without the dev server | `ddev npm run watch` |
| Open a shell in the web container | `ddev ssh` |
| Open a MariaDB shell | `ddev mysql` |
| Open Mailpit (all outgoing mail) | `ddev mailpit` |
| Keep or restore a copy of the database | `ddev snapshot`, `ddev snapshot restore <name>` |
| Rebuild the local database from scratch | `ddev artisan migrate:fresh --seed` |
| Show URLs and services | `ddev describe` |

Locally, `QUEUE_CONNECTION=sync` sends the queued notifications during the request, so no queue worker is needed.

> **Note:** DDEV uses MariaDB, while production uses MySQL. The two are compatible for everything this app
> does, but because parts of the app use hand-written SQL you may prefer to match production exactly with
> `ddev debug migrate-database mysql:8.0` (it converts the existing data; `ddev config --database` alone
> refuses to change the type of an existing database).

### npm supply-chain settings

`.npmrc` (and `e2e/.npmrc`) harden npm for this project. The Security workflow checks these settings:

- `ignore-scripts=true`: npm does not run install scripts of dependencies
- `min-release-age=7`: npm installs only package versions that are at least 7 days old
- `engine-strict=true`: npm refuses to install with a Node or npm version outside `engines` in `package.json`

Use `npm ci` to install exactly what `package-lock.json` specifies (`composer setup` does this).

## Tests

There are two suites. The PHP one runs inside DDEV; the browser suite runs on your host and talks to the
DDEV site over HTTPS.

| Suite | Command | What it covers |
|---|---|---|
| PHPUnit | `ddev composer test` | The session state machine (creation, accept/decline links, cancellations, notifications), the access rules of every route, the role checks, input validation, signed rating links, CV storage, CSV export, the status id contract, and the hand-written filter SQL. |
| End-to-end | `cd e2e && npm test` | Playwright: every route opens, mentor/mentee/matcher register, every menu option is clicked for all three roles, the CSV exports download. Captures a screenshot per screen. |

### PHPUnit

```bash
ddev composer test                                          # the whole suite
ddev coverage                                               # with a coverage report in coverage/
ddev exec vendor/bin/phpunit --filter testMethodName        # a single test
ddev exec vendor/bin/phpunit tests/Feature/FilterQueryTest.php   # a single file
```

The suite runs on a dedicated MariaDB database named `testing`, never on the development database `db`.
`phpunit.xml` sets `DB_DATABASE=testing` as a `<server>` value, which beats `.env` and environment variables; the host
and credentials still come from `.env`. `TestCase` refuses any other database and a cached configuration (`composer test` clears it
first). Feature tests use `RefreshDatabase`: each run migrates `testing` fresh and seeds the lookup tables. SQLite
does not work: an early migration drops a primary key column, and the filter SQL is MySQL-only. `ddev coverage`
turns Xdebug on for the run and then restores its previous state, because Xdebug slows down every request.

`tests/Feature/FilterQueryTest.php` is the filter-SQL regression check: every filter still returns the right
rows, placeholder and binding counts line up, apostrophes in names work, and injection payloads (`union select`,
quote breakout, `or 1=1`, `drop table`) are bound as values rather than parsed as SQL. Run it after any change to
the `*Manager` filter builders or to `RawQueryStorage`. `tests/Feature/MentorshipSessionLifecycleTest.php`
characterizes the session state machine — run it after touching `MentorshipSessionManager`.
`tests/Feature/RouteAccessTest.php` lists every public route; a new public route must be added there on purpose.

### End-to-end browser tests

A [Playwright](https://playwright.dev) suite drives a real browser against the DDEV site. It writes to the
DDEV database `db`, so take a `ddev snapshot` first if you want to keep your data. A screen counts
as broken if it returns 5xx **or** renders Laravel's exception page — a 200 with a stack trace in it still
fails.

```bash
cd e2e
npm ci
npx playwright install chromium   # first run only
npm run seed                      # a company plus one user per role; removes earlier e2e registrations
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

## Code quality

The project uses three tools:

- [Laravel Pint](https://laravel.com/docs/pint) for the PHP code style (rules in `pint.json`)
- [Larastan](https://github.com/larastan/larastan) (PHPStan level 8) for static analysis of `app/` (settings in
  `phpstan.neon`; known errors are in `phpstan-baseline.neon`, and new code must not add entries)
- [ESLint](https://eslint.org/) for `resources/assets/js`, `vite.config.mjs` and `eslint.config.mjs` (rules in
  `eslint.config.mjs`; the vendored theme JavaScript is not linted)

```bash
ddev composer lint:check   # run all three tools; report problems, change nothing
ddev composer lint:fix     # fix the PHP style and the JavaScript, then report what PHPStan still finds
ddev composer analyse      # PHPStan only
```

`.git-blame-ignore-revs` lists the format-only Pint commit; GitHub skips it in blame views, and locally
`git config blame.ignoreRevsFile .git-blame-ignore-revs` does the same.

## Continuous integration

GitHub Actions run the shared [SciFY workflows](https://github.com/scify/.github):

- `ci.yml` (on pushes to `master` and on pull requests): Pint, PHPStan, ESLint, the Vite build and the PHPUnit suite
  on PHP 8.4 with a MariaDB 11.8 service. The browser tests do not run in CI.
- `security.yml` (on pull requests and every Monday): `composer audit`, `npm audit`, the npm supply-chain settings,
  Gitleaks, committed environment files and dev tool configs, once for the root and once for `e2e/`

The `master` branch requires the check `ci / CI`; `dependabot_auto_merge.yml` relies on it.

## Releasing

Releases follow [Semantic Versioning](https://semver.org/): increase the **major** version for changes that break
users or deployments without a migration path, the **minor** version for new features and compatible changes, and the
**patch** version for bug fixes only. The version is defined only in `package.json`; `config('app.version')` reads
it, and the staff sidebar shows it. Releases are tagged `vX.Y.Z`. All changes are recorded in
[CHANGELOG.md](CHANGELOG.md), and the operator steps of each version in the upgrade notes of
[DEPLOYMENT.md](DEPLOYMENT.md#upgrade-notes).

Changes reach `master` through pull requests with a passing `ci / CI` check, so a release goes through a pull request
too. To release version `X.Y.Z` from an up-to-date `master` with green CI:

1. Create a branch, for example `git switch -c chore/release-vX.Y.Z`.
   - In `CHANGELOG.md`, rename `## [Unreleased]` to `## [X.Y.Z] - YYYY-MM-DD`, add a new empty `## [Unreleased]`
     above it, and update the compare links at the end of the file. Write links in the section as absolute URLs:
     the section becomes the GitHub release notes, which do not resolve relative links.
   - In `DEPLOYMENT.md`, rename `### Unreleased` to `### X.Y.Z` and add a new `### Unreleased` above it.
2. Set the version (this updates `package.json` and `package-lock.json`, without a commit or tag):

   ```bash
   ddev npm version X.Y.Z --no-git-tag-version
   ```

3. Commit, push and open a pull request; merge it with a merge commit when CI passes:

   ```bash
   git add CHANGELOG.md DEPLOYMENT.md package.json package-lock.json
   git commit -m "chore: release vX.Y.Z"
   git push -u origin chore/release-vX.Y.Z
   gh pr create --title "chore: release vX.Y.Z" --body "Release vX.Y.Z. See CHANGELOG.md."
   ```

4. Tag the merge commit of the release pull request and push the tag. Tag that commit by its hash: another pull
   request may have been merged after it.

   ```bash
   git switch master && git pull --ff-only
   gh pr view chore/release-vX.Y.Z --json mergeCommit --jq .mergeCommit.oid   # the merge commit
   git tag -a vX.Y.Z <merge-commit> -m "vX.Y.Z"
   git push origin vX.Y.Z
   ```

5. Create the GitHub release, with the changelog section as the release notes:

   ```bash
   awk '/^## \[X.Y.Z\]/{found=1; next} /^## \[/{found=0} found' CHANGELOG.md > release-notes.md
   gh release create vX.Y.Z --title "vX.Y.Z" --notes-file release-notes.md --verify-tag
   rm release-notes.md
   ```

6. Deploy the tag, not the branch ([DEPLOYMENT.md](DEPLOYMENT.md#update-procedure)). For staging, set `DEPLOY_REF`
   to the tag in the private repository `scify/jobpairs-deploy` and run its workflow.

## Troubleshooting

**`ViteManifestNotFoundException`** — the frontend assets have not been built. Run `ddev composer setup` (or
`ddev npm run build`).

**`fopen(...storage/framework/cache/...): Failed to open stream: No such file or directory`** — the cache
directory is not writable by the user running PHP, and Laravel reports the failed `mkdir` as a missing file.
This usually happens after PHP ran as a different user. Clear the stale files from inside the container and let
them be recreated:

```bash
ddev exec sudo rm -rf storage/framework/cache/* storage/framework/views/* storage/framework/sessions/*
ddev artisan optimize:clear
```

**Registration fails with "An error occurred. Please try again."** — the app sends a notification on signup,
so it needs a reachable mail server. In DDEV, `MAIL_HOST` must be `127.0.0.1` and `MAIL_PORT` `1025`
(Mailpit); `ddev restart` writes these values into `.env`.

## Deployment

[DEPLOYMENT.md](DEPLOYMENT.md) describes the server: the production `.env`, nginx, the Supervisor programs for the
queue worker and the scheduler, the first installation, the update procedure and the upgrade notes of each
version.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is open-sourced software licensed under
the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).

JobPairs has been created by [Science For You (SciFY)](https://www.scify.org), a Greek not-for-profit
organization.

### Credits

Icons used in this project are made by

- [Freepik](http://www.flaticon.com/authors/freepik) from [www.flaticon.com](http://www.flaticon.com)
- [Vectors Market](http://www.flaticon.com/authors/vectors-market) from [www.flaticon.com](http://www.flaticon.com)
- [Prosymbols](http://www.flaticon.com/authors/prosymbols) from [www.flaticon.com](http://www.flaticon.com)
