# AGENTS.md

Guidance for AI coding agents working in this repository. `CLAUDE.md` imports this file. Commands and setup are in
`README.md`, the server in `DEPLOYMENT.md`; changes are recorded in `CHANGELOG.md`.

## Project

JobPairs: a Laravel 13 backoffice (server-rendered Blade + jQuery, no SPA, no JSON API) that matches **mentors**
with **mentees**. They register through public forms (`MentorProfile` / `MenteeProfile`, tables `mentor_profile` /
`mentee_profile`). **Staff** (`User`, table `users`) are administrators, matchers and account managers: matchers pair
them in a **mentorship session** (`MentorshipSession`), account managers follow it until the evaluation.

## Working rules

- **Run everything in DDEV** (`ddev composer …`, `ddev artisan …`, `ddev npm …`, `ddev exec …`). Only the browser
  tests (`e2e/`) run on the host.
- **Ask Paul before `ddev start`, `ddev restart` and `ddev import-db`.** Start and restart run `composer setup` and
  `php artisan migrate` on the DDEV database (post-start hook); `import-db` replaces the database.
- **The DDEV database `db` is Paul's.** Ask before `migrate`, `db:seed` (also one seeder class), tinker writes, the
  browser tests or `npm run seed` against it. Most seeders empty their tables first; `UserTableSeeder` overwrites
  users 1-3 and their passwords. Run browser checks between `ddev snapshot` and `ddev snapshot restore`, or create
  temporary data and delete it by id.
- `php artisan anonymize:emails` and `php artisan fix:duplicates` change data in bulk; never run them without Paul.
- **Commit gate**: run each tool as its own command and read its exit code, then commit in a separate command:
  `ddev composer lint:check` (Pint, Larastan, ESLint) and `ddev exec env COLUMNS=120 vendor/bin/phpunit`. After a
  change to routes or views, also run the browser tests (README "End-to-end browser tests"), with Paul's approval.
- Without a terminal, DDEV sets `COLUMNS=0`; scripted runs pass `COLUMNS` (`ddev coverage` does this itself).
- When a change removes code that a `phpstan-baseline.neon` entry ignores, remove that entry in the same commit
  (PHPStan fails on unmatched entries). New and changed code passes level 8 without new baseline entries.
- New npm packages: pick versions released at least 7 days ago; `.npmrc` `min-release-age=7` refuses newer ones.
- `composer require` / `composer update` without `-W`; use `-w` with the exact packages instead.
- `master` requires the `ci / CI` check (ruleset), and changes reach it through pull requests. Work on a branch; ask
  before every push, force push, tag and release. Merge with a merge commit: `.git-blame-ignore-revs` lists a commit
  hash.
- Commits are authored by Paul only, without `Co-Authored-By` lines. Pull request descriptions have no "Test plan"
  section and no mention of AI tools.
- Every change gets a line under `## [Unreleased]` in `CHANGELOG.md`. A change that needs a server action (an
  `.env` key, a one-off command, a data check) also gets a step in the "Unreleased" upgrade notes of
  `DEPLOYMENT.md`. The version lives only in `package.json`; releases follow README "Releasing".
- Never truncate or delete `storage/logs/*`; read new lines with `tail -n +N`.

## Tests

- **PHPUnit runs on the MariaDB database `testing` only.** Keep all guards: the `<server>` lines in `phpunit.xml`
  (they beat `.env` and environment variables), `TestCase`'s refusal of any other database and of a cached config
  (`tests/Unit/*Guard*Test.php`). `RefreshDatabase` wipes the database it runs on. SQLite does not work: migration
  `2017_03_22_152036` drops a primary key column, and the filter SQL is MySQL-only.
- `TestCase` sets `$seed = true` (the fixtures need the lookup tables) and calls `withoutVite()`, so PHPUnit does not
  need `public/build`.
- Fixtures: `tests/Support/CreatesMatchingFixtures.php` (`makeBackofficeUser($roleId, …)`, companies, profiles,
  `makeSessionAtStatus()`).
- `tests/Feature/RouteAccessTest.php` reads the router: a new public route must be added to its public list on
  purpose, and every other route must sit behind `'auth', 'staff'`.
- For an access rule, remove the check once and watch the test fail. A denied role check (`DeniesAccess`) answers
  no 403: it redirects to the session's previous URL (never the `Referer`) with the error "You have no access to this
  page"; assert the redirect, `assertSessionHasErrors()` and the unchanged data.
- `tests/Unit/InlineScriptsTest.php`: inline Blade scripts that use jQuery must be `type="module"`.
- The browser tests need `npm run seed` first. Turn the debug bar off for the run: add `DEBUGBAR_ENABLED=false` to
  `.env`, run `ddev artisan config:clear`, and remove the line afterwards. The debug bar adds HTML to the plain
  `NO_DATA_FOUND` answer of an empty CSV export, and `05-exports` fails.
- Feature tests to know: `MentorshipSessionLifecycleTest` (the state machine and who gets which mail),
  `FilterQueryTest` (the filter SQL and injection payloads; run it after changing a filter builder or
  `RawQueryStorage`), `UserAccessManagerTest` (role checks).

## Domain and architecture

- Layers: route → controller (`app/Http/Controllers`, thin, creates managers with `new`) → manager
  (`app/BusinessLogicLayer/managers/*Manager`, business rules, notifications, view models in
  `app/Models/viewmodels`) → storage (`app/StorageLayer/*Storage`, the only place for queries; `RawQueryStorage`
  holds the hand-written filter SQL, always with bindings) → model (`app/Models/eloquent`). Keep a change in the
  layer it belongs to.
- Roles (`UserAccessManager`, no Policies or Gates): 1 administrator, 2 matcher, 3 account manager. Every check
  reads the roles from the database; do not add a cache without invalidation on role changes.
- Access: every protected route group starts with `'auth', 'staff'`. `staff` (`app/Http/Kernel.php`) is
  `EnsureActiveStaff` (`User::isActiveStaff()`: `state_id` 1 and at least one role) and `SameOriginFrames` (staff
  pages cannot be framed; the public forms stay embeddable). The role groups after it are listed in
  `routes/web.php`. Middleware checks the role; the manager checks ownership and throws:
  `updateMentorshipSessionAsUser()` (an account manager only for their own sessions), `inviteMentee()`, the session
  history delete (author or administrator), and the company assignment (only an account manager can be assigned,
  and only an administrator can give away a company that has another account manager). The own-capacity rule is the
  `can-edit-user-profile` middleware. Policy (Paul): all staff may read all data.
- Session lifecycle: a status state machine in `MentorshipSessionManager` (pending → introduction_sent →
  available_mentee / available_mentor → started → first..fourth_meeting → evaluation_sent → follow_up_sent, plus the
  cancellation states `cancelled_mentee` / `cancelled_mentor` / `cancelled_acc_man`). The fourth meeting advances to
  evaluation_sent by itself. Use the buckets of `app/Utils/MentorshipSessionStatuses.php` instead of status ids.
- Email links: accept, decline and set-available links are unauthenticated GET routes, matched only by id and email
  in the URL; Paul keeps them unsigned, they are throttled (`throttle:30,1,email-links`). The rating links are
  `signed:relative`: the mails build them with `URL::temporarySignedRoute(…, 30 days, absolute: false)` behind
  `APP_URL`, and the form posts back to the same signed URL.
- Every class in `app/Notifications` is `ShouldQueue`; only the framework password reset mail goes out at once. On
  servers Supervisor runs `queue:work` and `schedule:work`; the scheduler starts no worker
  (`tests/Unit/ScheduleTest.php`). Locally `QUEUE_CONNECTION=sync`. Links in mails come from `APP_URL`, because the
  worker has no request.
- Guests: `throttle:public-forms` (`RouteServiceProvider`, 10 per 10 minutes, active staff exempt) on the public
  create forms. Guests cannot set a mentor's or mentee's status or pick private specialties; the server sets the
  creator and the CV file name.
- CVs: `CvFileStorage`, private disk folder `storage/app/private/cv_files`, random names for new uploads, staff
  download only. The
  old public folder `storage/app/public/uploads/cv_files` is read as a fallback until `cv:move-to-private` runs.
- Output: stored profile and company links go through `WebUrl::orNull()` (http/https only); CSV cells through
  `DataToCsvExportManager::cell()` (formula escaping); `lang` from a request through
  `Controller::localeFromRequest()` (`en`, `gr`).
- Other parts: lookup data (`Company`, `Industry`, `Specialty`, `Residence`, `EducationLevel`, `University`,
  `Reference`, each with manager, storage and seeder); `MentorStatus` / `MenteeStatus` with `*StatusHistory` audit
  tables (availability, separate from the session status); `MentorshipSessionHistory`; `MentorRating` /
  `MenteeRating`; `AccountManagerCapacity` (active sessions per account manager); the pivots `MentorSpecialty`,
  `MenteeSpecialty`, `MentorIndustry`. CSV exports (`ReportController`, `export/mentors|mentees|sessions`, admin
  only) go through `DataToCsvExportManager`. Translations `en` and `gr` in
  `lang/`; `app/ViewComposers/MenteeAndMentorMenuComposer.php` feeds the menus. Sentry reports backend
  (`SENTRY_LARAVEL_DSN`) and browser (`VITE_SENTRY_DSN_PUBLIC`) errors.
- The servers set the visitor address in nginx (Cloudflare `real_ip`); the app trusts no proxy.

## Frontend

- Vite entries are listed in `vite.config.mjs`; Blade loads them with `@vite`. Inline Blade scripts are
  `type="module"`, so they run after the bundles.
- jQuery: `resources/assets/js/jquery-global.js` sets `window.$` first; first-party modules `import $ from 'jquery'`
  (ESLint has no global `$`).
- Bootstrap stays at 3.4.1 (no patched 3.x; README "Known npm advisories"). Never put user data in `title`,
  `data-*` or `data-loading-text` of elements that match `[data-toggle="tooltip"]`, `[data-toggle="popover"]`,
  `.demo-btn-loading` or `[id^=tooltip-]`.
- Pass server data to scripts with `@json`, and insert text with jQuery `.text()` / `.attr()`, never `.html()` with
  values read from the DOM: names from the public forms reach the staff pages.

## Known issues

- The accept, decline and set-available email links carry no signature (Paul's decision); anyone with a link can use
  it.
- The login, password reset and public form pages can be framed by other sites; only staff pages send
  `frame-ancestors 'self'`.
- The password reset form tells whether an address exists (laravel/ui default; throttled).
- Some controllers show exception messages to staff after a failed action.
- `npm run prod` is a deprecated alias of `npm run build`; remove it in the next release.
- `phpstan-baseline.neon` holds about 700 entries: missing types of the old code and 23 reviewed level 8 findings.
