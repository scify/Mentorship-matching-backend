# End-to-end suite

Playwright tests that drive the running DDEV site through a real browser.
They exist because the PHP side has effectively no automated coverage, so the
only way to know a framework upgrade did not break a screen is to open it.

## What is covered

| Spec | What it asserts |
| --- | --- |
| `01-public-registration` | A mentor and a mentee register through the public forms and get a success flash. |
| `02-matcher-registration` | An admin creates a Matcher via `/user/create`; the new user appears in the list, can log in, and their role grants `/sessions/myMatches`. |
| `03-routes` | Every page-rendering GET route opens for the role that may reach it — public pages, admin pages, ajax fragments, and the `{id}` detail/edit pages of a real mentor, mentee and user. |
| `04-navigation` | Every sidebar option for **all three roles** is expanded, clicked and checked; the mentor/mentee filter panels submit; global search returns a panel. The filter endpoints are also probed with SQL injection payloads and must neither 5xx nor leak `SQLSTATE`. |
| `05-exports` | The three admin CSV exports (`/export/mentors`, `/export/mentees`, `/export/sessions`) return a real CSV attachment (or `NO_DATA_FOUND` on an empty table) without 5xx-ing or leaking `SQLSTATE` — these run hand-written SQL and are the only admin routes the route-walking specs don't cover. |

A route counts as broken if it returns 5xx **or** renders Laravel's exception
page — a 200 that happens to contain a stack trace is still a failure.

## Running

DDEV must be up (`ddev start` installs the dependencies, builds the assets and runs
the migrations). The suite writes to the DDEV database `db`: it registers mentors
and mentees, and `npm run seed` adds fixture users and removes the rows of earlier
runs. Take a snapshot first if you want to keep your data:

```bash
ddev snapshot --name before-e2e      # restore with: ddev snapshot restore before-e2e
ddev exec php artisan db:seed        # only on an empty database: the lookup tables
```

Then seed the fixtures the suite needs (a company plus one user per role) and run:

```bash
cd e2e
npm ci
npx playwright install chromium   # first time only
npm run seed
npm test
```

`npm run seed` is idempotent — re-running it updates the fixture users rather
than duplicating them.

Turn the Laravel debug bar off for the run (`DEBUGBAR_ENABLED=false` in `.env`,
then `ddev artisan config:clear`). The debug bar adds its HTML to the plain
`NO_DATA_FOUND` answer of an empty CSV export, so `05-exports` fails when the
database has no sessions.

### Notes

- The suite talks to `https://mentorship-matching-backend.ddev.site`. Override with
  `E2E_BASE_URL`.
- Emails are sent on registration. DDEV catches them in Mailpit (`ddev mailpit`).
- Set `DEBUGBAR_ENABLED=false` in `.env`. The debugbar overlay intercepts clicks
  and appears in every screenshot.
- Screenshots land in `e2e/screenshots/`, numbered in capture order
  (`<spec>-<n>-<page>.png`). The directory is gitignored.
- Fixture users are `admin@`/`matcher@`/`accman@jobpairs.test`, all with the
  password `password123`. They are test credentials for a local stack only.
