# End-to-end suite

Playwright tests that drive the running Docker stack through a real browser.
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

The stack must be up and the assets built:

```bash
docker compose up -d
docker exec mentorship_matching_platform_server bash -c "cd /var/www && composer install && npm install && npm run prod"
docker exec mentorship_matching_platform_server php artisan migrate:fresh --seed --force
```

Then seed the fixtures the suite needs (a company plus one user per role) and run:

```bash
cd e2e
npm install
npx playwright install chromium   # first time only
npm run seed
npm test
```

`npm run seed` is idempotent — re-running it updates the fixture users rather
than duplicating them.

### Notes

- The suite talks to `http://localhost:89` (the `nginx` service). Override with
  `E2E_BASE_URL`.
- Emails are sent on registration, so the `mailhog` service must be running and
  `MAIL_HOST` must be `mailhog` — not `localhost`, which resolves to the PHP
  container itself.
- Set `DEBUGBAR_ENABLED=false` in `.env`. The debugbar overlay intercepts clicks
  and appears in every screenshot.
- Screenshots land in `e2e/screenshots/`, numbered in capture order
  (`<spec>-<n>-<page>.png`). The directory is gitignored.
- Fixture users are `admin@`/`matcher@`/`accman@jobpairs.test`, all with the
  password `password123`. They are test credentials for a local stack only.
