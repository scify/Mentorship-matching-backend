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
| Assets | Laravel Mix (webpack) |

## Table of Contents

- [Mentorship matching backend platform](#mentorship-matching-backend-platform)
  - [Table of Contents](#table-of-contents)
  - [First time install](#first-time-install)
    - [DDEV option (recommended)](#ddev-option-recommended)
    - [Docker Compose option](#docker-compose-option)
    - [Non-Docker option](#non-docker-option)
      - [Frontend dependencies](#frontend-dependencies)
      - [Backend dependencies](#backend-dependencies)
        - [Composer installation](#composer-installation)
        - [Apache configuration](#apache-configuration)
        - [Laravel local server](#laravel-local-server)
  - [Setup the Database](#setup-the-database)
    - [Add seed data to DB](#add-seed-data-to-db)
  - [Compiling assets](#compiling-assets)
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

After cloning the project, create an `.env` file (should be a copy of `.env.example`),
containing the information about your database name and credentials.

```bash
cp .env.example .env
```

### DDEV option (recommended)

[DDEV](https://ddev.readthedocs.io/en/stable/#installation) reads the committed `.ddev/config.yaml`, so it
provisions the right PHP, Node and database versions with no further setup.

```bash
ddev start
ddev composer install
ddev npm install
ddev npm run prod          # compile assets - without them every page 500s
ddev exec php artisan key:generate
ddev exec php artisan migrate --seed
```

The site is then served at <https://mentorship-matching-backend.ddev.site>, and captured mail is at
`ddev launch -m` (Mailpit).

To check the install actually works, run the suites in [Testing](#testing) — `ddev exec php
tests/manual/verify_filters.php` is the quickest confirmation that the app and database are wired up.

`ddev start` rewrites the database and mail settings in your `.env` to match the services it runs, so you do
not need to edit them by hand.

Run any backend command through DDEV:

```bash
ddev exec php artisan migrate
ddev composer install
ddev npm run watch
ddev ssh                   # shell inside the web container
```

> **Note:** the DDEV config uses MariaDB, while `docker-compose.yml` and production use MySQL. The two are
> compatible for everything this app does, but because parts of the app use hand-written SQL you may prefer to
> match production exactly with `ddev config --database=mysql:8.0 && ddev restart`.

### Docker Compose option

The `docker-compose.yml` file at the project root brings up `php`, `nginx`, `db` (MySQL), `redis` and
`mailhog`.

```bash
docker compose build
docker compose up -d
```

Then enter the container to run `php artisan`, `composer` and `npm` commands:

```bash
docker exec -it mentorship_matching_platform_server bash
composer install
npm install && npm run prod
php artisan key:generate
php artisan migrate --seed
```

The site is served at <http://localhost:89> and captured mail at <http://localhost:8100>.

With this stack `MAIL_HOST` must be `mailhog` — the name of the service. `localhost` resolves to the PHP
container itself and registration emails will fail.

### Non-Docker option

#### Frontend dependencies

Install the Node version listed in `.nvmrc`:

```bash
nvm use    # reads .nvmrc
node -v    # v24.x
```

Node 24 or newer is required — `select2` will refuse to install on older versions.

#### Backend dependencies

PHP 8.3 or newer, with the extensions Laravel requires:

```bash
sudo apt install php8.4 php8.4-cli php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-gd php8.4-intl mysql-server
```

##### Composer installation

Install composer globally by following [the instructions](https://getcomposer.org/download/).

##### Apache configuration

Edit the `/etc/apache2/sites-available/mentorship-matching.conf` so that it looks like:

```text
<VirtualHost *:80>
    ServerName dev.mentorship-matching
    DocumentRoot "/path/to/Mentorship-matching-backend/public"
    <Directory "/path/to/Mentorship-matching-backend/public">
        AllowOverride all
    </Directory>
</VirtualHost>
```

Make the symbolic link:

```bash
cd /etc/apache2/sites-enabled && sudo ln -s ../sites-available/mentorship-matching.conf
```

Enable `mod_rewrite` and restart the server:

```bash
sudo a2enmod rewrite && sudo service apache2 restart
```

Fix permissions for storage directory:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

Test the setup by navigating to `http://dev.mentorship-matching` in your browser.

##### Laravel local server

You can also test your setup by running the Laravel local server:

```bash
php artisan serve
```

and navigating to [localhost:8000](http://localhost:8000).

## Setup the Database

Laravel provides a simple yet powerful mechanism for creating the DB schema,
called [Migrations](https://laravel.com/docs/13.x/migrations).
Simply run `php artisan migrate` to create the appropriate DB schema.

### Add seed data to DB

Run `php artisan db:seed` in order to insert the starter data to the DB by
using [Laravel seeder](https://laravel.com/docs/13.x/seeding). This creates the lookup tables (roles,
statuses, specialties, universities) and a few staff accounts.

To rebuild a local database from scratch:

```bash
php artisan migrate:fresh --seed
```

## Compiling assets

When in project root directory, run

```bash
npm run dev      # development build
npm run watch    # rebuild on change
npm run prod     # production build
```

The app reads `public/mix-manifest.json`, which is **not** committed. If you have not built the assets, every
page fails with `MixManifestNotFoundException`.

## Testing

There are three suites. The PHP ones run inside your stack; the browser suite runs on your host and talks
to the app over HTTP.

| Suite | Command | What it covers |
|---|---|---|
| PHPUnit | `vendor/bin/phpunit` | Smoke test only — one test asserting the guest redirect. |
| Filter queries | `php tests/manual/verify_filters.php` | 58 assertions on the hand-written filter SQL: every filter, binding order, and SQL injection payloads. |
| End-to-end | `cd e2e && npm test` | 15 Playwright tests: every route opens, mentor/mentee/matcher register, every menu option is clicked for all three roles. Captures a screenshot per screen. |

Run the PHP suites through whichever stack is up — they need PHP and a database, so they will not work
from the host unless you have both installed locally:

```bash
# DDEV
ddev exec vendor/bin/phpunit
ddev exec php tests/manual/verify_filters.php

# Docker Compose
docker exec mentorship_matching_platform_server bash -c "cd /var/www && vendor/bin/phpunit"
docker exec mentorship_matching_platform_server php /var/www/tests/manual/verify_filters.php
```

### PHPUnit

`phpunit.xml` forces `APP_ENV=testing` with array cache/session drivers. The suite is a smoke test — the
manager and storage layers, where the business logic lives, have no unit coverage. Don't read a green run
as "my change is safe"; exercise the affected screen.

```bash
ddev exec vendor/bin/phpunit --filter testMethodName   # a single test
```

### Filter query regression check

The mentor, mentee and session filter screens build SQL by hand. This script asserts that every filter still
returns the right rows, that placeholder and binding counts line up, that apostrophes in names work, and that
injection payloads (`union select`, quote breakout, `or 1=1`, `drop table`) are bound as values rather than
parsed as SQL.

```bash
ddev exec php tests/manual/verify_filters.php
```

It creates and removes its own fixtures, so it is safe to re-run — but point it at a development database,
not production. Run it after any change to the `*Manager` filter builders or to `RawQueryStorage`.

### End-to-end browser tests

A [Playwright](https://playwright.dev) suite drives a real browser against the running app. A screen counts
as broken if it returns 5xx **or** renders Laravel's exception page — a 200 with a stack trace in it still
fails.

```bash
cd e2e
npm install
npx playwright install chromium   # first run only
npm run seed                      # a company plus one user per role
npm test
```

Point it at whichever stack you are running:

```bash
E2E_BASE_URL=https://mentorship-matching-backend.ddev.site npm test   # DDEV
E2E_BASE_URL=http://localhost:89 npm test                             # docker compose (the default)
```

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

`npm run seed` (or `php e2e/fixtures/seed.php`) creates one account per role, all with the password
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

**`MixManifestNotFoundException`** — the frontend assets have not been built. Run `npm install && npm run prod`.

**`fopen(...storage/framework/cache/...): Failed to open stream: No such file or directory`** — the cache
directory is not writable by the user running PHP, and Laravel reports the failed `mkdir` as a missing file.
This usually happens after switching between environments that run PHP as different users. Clear the stale
files and let them be recreated:

```bash
rm -rf storage/framework/cache/* storage/framework/views/* storage/framework/sessions/*
php artisan optimize:clear
```

(If the files are owned by another user, delete them from inside the container: `ddev exec sudo rm -rf ...`.)

**Registration fails with "An error occurred. Please try again."** — the app sends a notification on signup,
so it needs a reachable mail server. Check `MAIL_HOST` matches your stack: `mailhog` for docker compose,
`127.0.0.1` for DDEV.

**Role changes do not take effect** — role checks are cached without invalidation. Run `php artisan cache:clear`.

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
