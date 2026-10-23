# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html). The version is defined in `package.json`; see
"Releasing" in `README.md`. What an operator must do to install a version is in the upgrade notes of
[DEPLOYMENT.md](DEPLOYMENT.md#upgrade-notes).

## [Unreleased]

## [2.0.0] - 2026-10-23

### Upgrade notes

This release changes how the application is installed, built, configured, run and secured. Follow the
[2.0.0 upgrade notes](https://github.com/scify/Mentorship-matching-backend/blob/v2.0.0/DEPLOYMENT.md#200) in DEPLOYMENT.md. In short:

- PHP 8.4 with `pcntl` for the CLI; Node.js `^22.23.4`, `^24.22.1` or `^26.12.1` and npm 11.19.1 or newer for the
  build.
- Supervisor runs the queue worker and the scheduler. Set it up before the deploy, or the mails wait in the
  `jobs` table.
- New and changed `.env` values: `APP_URL`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `LOG_LEVEL`,
  `QUEUE_CONNECTION=database`, `SENTRY_SQL_BINDINGS`, `VITE_SENTRY_DSN_PUBLIC` (was `MIX_SENTRY_DSN_PUBLIC`).
- Two read-only checks before the deploy; `php artisan cv:move-to-private` and `php artisan storage:unlink` after
  it.

### Security

- The hand-written filter queries bind all user input; before, search and filter values reached the SQL
  unescaped (#120).
- The unauthenticated `/server-info` page that ran `phpinfo()` is gone (#120).
- Laravel 8 → 13 and PHP 7.4 → 8.4 (#120); the October 2026 updates: Laravel 13.38 (the minimum is now 13.36.0,
  `^13.36.0`), Node.js 24.22.1, Vite 8.3.4 and other npm build tools (#125).
- A stored XSS is fixed: names from the public registration forms ran as HTML in the staff match modal (#130).
- Access control (#135):
  - Every staff page needs an active account with at least one role.
  - Role checks read the roles on every request, so a revoked role stops at once.
  - Account managers change only the sessions they manage and their own capacity; only the author or an
    administrator deletes a session history item; an account manager claims only a company without another
    account manager.
  - Changing the account manager capacity and inviting the mentee of a session are POST requests, not GET.
- Input (#136):
  - The mentor and mentee forms accept an explicit list of fields; the creator and the CV file name are set by
    the server, and only staff can set the status.
  - Session, account manager and icon ids are validated; guests cannot create or pick private specialties.
  - Profile and company links must be http or https URLs, and the pages link only such values.
  - CSV exports escape cells that a spreadsheet would run as a formula.
- Public surface (#137):
  - The `/register` page, the API route and the test routes are gone.
  - The public forms, the email links and the password reset form are throttled; logged-in staff are exempt
    from the form limit.
  - Email addresses no longer appear in the log of a mismatched accept link; Sentry no longer receives SQL
    values by default (`SENTRY_SQL_BINDINGS`).
  - The session cookie is `SameSite=Lax`.
- More hardening (#138):
  - Uploaded CVs are private, with random names, and download only through the app.
  - Changing your own email or password needs the current password; passwords may be up to 255 characters
    (was 12).
  - Staff pages cannot be framed by other sites.
  - A denied request returns to the previous page of the session, never to the `Referer`.
- The rating links in the evaluation mails are signed and valid for 30 days; links in mails sent before this
  release no longer work (403). The password reset link is built from `APP_URL`, not from the request host
  (#139).
- In maintenance mode, `public/index.php` answers before it loads `vendor/`, so a deploy no longer serves requests
  from a half-replaced `vendor/` (#140).

### Added

- The app version, read from `package.json`, at the bottom of the staff sidebar.
- `DEPLOYMENT.md`: the server setup, the Supervisor programs, the update procedure and the upgrade notes per
  version (#140).
- The `cv:move-to-private` command, which moves the CVs uploaded before this release to private storage (#138).
- DDEV (#120), now the only local environment, with `composer setup` and `ddev coverage` (#128).
- Tests:
  - PHPUnit suite on a dedicated `testing` database, with guards that refuse any other database (#127).
  - Tests for the access rules of every route (#134), the input rules, the email and rating links, the CVs, the
    CSV export and the schedule.
  - Playwright end-to-end suite (#120).
- Code quality and CI: Laravel Pint (#132), Larastan (#120) at level 8 (#133), ESLint (#130), and the shared SciFY
  CI and security workflows (#131); Dependabot opens security updates only, and `dependabot_auto_merge.yml`
  merges them when CI passes.

### Changed

- Vite replaces Laravel Mix; `npm run build` builds the assets (#122).
- Supervisor runs the queue worker and the scheduler. The scheduler no longer starts `queue:work` every two
  minutes (#140).
- npm follows the SciFY policy: `engine-strict`, `ignore-scripts`, `min-release-age=7` and minimum Node and npm
  versions, also for `e2e/` (#126, #129).
- README, CONTRIBUTING and `.env.example` are rewritten; `LOG_LEVEL` replaces `APP_LOG_LEVEL` (#141).

### Deprecated

- `npm run prod`: an alias of `npm run build`, kept for this release only (#122).

### Removed

- The Docker Compose development setup (#128).
- Unused npm packages: `vue`, `vue-resource`, `modernizr`, `bootstrap-sass`, `bxslider`, `font-awesome`, `lodash`
  and the Laravel Mix toolchain (#122, #123, #129).
- The `.env.example` keys `APP_NAME`, `EMAIL_FOR_CC`, `MENTORS_EXCEL_FILE_PATH` and `MENTEES_EXCEL_FILE_PATH`,
  which nothing read (#141).

### Fixed

- The email link pages always rendered in English; they now follow `lang=gr` (#133).
- A forged `lang` value made pages answer 500; only `en` and `gr` are accepted (#133).
- The university lists could contain `null`, which broke the forms (#133).
- The unmatched-mentees query binds its month count instead of concatenating it into the SQL (#133).
- A JavaScript error from FastClick on slow page loads (#129).
- Bootstrap, bootstrap-select and Velocity loaded twice, so each plugin ran twice (#130).

## [1.0.0]

The state of the application before release tags were introduced (commit `a09a086`, 2026-08-11). It is not
tagged.

[Unreleased]: https://github.com/scify/Mentorship-matching-backend/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/scify/Mentorship-matching-backend/compare/a09a086...v2.0.0
[1.0.0]: https://github.com/scify/Mentorship-matching-backend/tree/a09a086
