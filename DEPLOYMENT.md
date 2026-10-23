# Deployment

How to install, update and run JobPairs on a server. For local development, see the [README](README.md).

The examples use `/var/www/jobpairs` as the application directory, `www-data` as the web server user and
`/usr/bin/php8.4` as the PHP binary. Replace them with the values of your server.

## Contents

- [What runs on the server](#what-runs-on-the-server)
- [Requirements](#requirements)
- [Production `.env`](#production-env)
- [Web server](#web-server)
- [Supervisor: queue worker and scheduler](#supervisor-queue-worker-and-scheduler)
- [First installation](#first-installation)
- [Update procedure](#update-procedure)
- [Upgrade notes](#upgrade-notes)
- [Troubleshooting](#troubleshooting)

## What runs on the server

| Part | What it does |
|---|---|
| nginx + PHP-FPM | Serves `public/`. |
| MySQL | The application database. |
| Supervisor: `jobpairs-worker` | Runs `php artisan queue:work`. It sends the queued email notifications (`QUEUE_CONNECTION=database`). |
| Supervisor: `jobpairs-scheduler` | Runs `php artisan schedule:work`. It starts `mentees:notify-unmatched` and `email:follow-up` once a day. |
| SMTP server | Receives the outgoing mail. |

Node.js is needed only to build the frontend assets. The build can run on the server or on a build machine (the
staging workflow builds on a CI runner and copies `public/` to the server).

## Requirements

- PHP 8.4 (`composer.json`) with the extensions that Laravel 13 needs, plus `pdo_mysql`.
- Composer 2.
- MySQL. Local development and CI use MariaDB 11.8, which is compatible for everything this app does.
- Node.js 24 (`.nvmrc`; `package.json` also accepts 22 and 26) and npm 11.19 or later, for the build only.
- Supervisor.
- HTTPS. The session cookie is marked secure (see `SESSION_SECURE_COOKIE`).

## Production `.env`

Start from `.env.example`. These keys need a production value:

| Key | Production value | Why |
|---|---|---|
| `APP_ENV` | `production` (`staging` on staging) | |
| `APP_DEBUG` | `false` | `.env.example` ships `true`, which shows stack traces and configuration to visitors. |
| `APP_KEY` | generated once, then kept | A new key logs out every user and breaks the signed rating links in sent mails. |
| `APP_URL` | the public URL, for example `https://backoffice.example.org` | The password reset links and the rating links in mails are built from it. |
| `DB_*` | the production database | |
| `QUEUE_CONNECTION` | `database` | The Supervisor worker sends the mails. With `sync`, a slow mail server slows every request that sends mail. |
| `MAIL_*` | the SMTP server and sender | |
| `SESSION_SECURE_COOKIE` | `true` | The browser sends the session cookie over HTTPS only. |
| `LOG_LEVEL` | `warning` or `error` | `.env.example` leaves the default `debug`. |
| `SENTRY_LARAVEL_DSN` | the project DSN, or empty | Empty disables Sentry. |
| `SENTRY_SQL_BINDINGS` | `false` | `true` sends SQL values, including personal data, to Sentry. |
| `VITE_SENTRY_DSN_PUBLIC` | the public DSN, or empty | Read at build time: set it before `npm run build`. |

The app caches its configuration in production (`php artisan optimize`). After every change to `.env`, run
`php artisan optimize` again; otherwise the old values stay in use.

## Web server

- The document root is `public/`. Nothing above `public/` may be served.
- Pin `server_name` to the public host name, and reject other hosts with a default server. The app builds some
  absolute links from the request.
- Behind Cloudflare, set the visitor address in nginx (`set_real_ip_from` + `real_ip_header CF-Connecting-IP`; on
  SciFY servers this is `/etc/nginx/conf.d/cloudflare-realip.conf`). The app has no trusted proxy list. Without
  this setting, the request throttles count all visitors as one client.
- The app sends `X-Frame-Options` and `Content-Security-Policy: frame-ancestors 'self'` on the staff pages
  itself. The public registration forms stay embeddable on purpose.
- The app does not need `php artisan storage:link`. Uploaded CVs are private (`storage/app/private/cv_files`) and
  are downloaded through the app.

## Supervisor: queue worker and scheduler

Create `/etc/supervisor/conf.d/jobpairs.conf`:

```ini
[program:jobpairs-worker]
command=/usr/bin/php8.4 /var/www/jobpairs/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/jobpairs
user=www-data
numprocs=1
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
stopwaitsecs=60
redirect_stderr=true
stdout_logfile=/var/www/jobpairs/storage/logs/worker.log

[program:jobpairs-scheduler]
command=/usr/bin/php8.4 /var/www/jobpairs/artisan schedule:work
directory=/var/www/jobpairs
user=www-data
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
redirect_stderr=true
stdout_logfile=/var/www/jobpairs/storage/logs/scheduler.log
```

Then load it:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

- Run the scheduler from Supervisor only. Do not also add a `schedule:run` cron entry, or the daily mails go out
  twice.
- The worker keeps the application code in memory. Every deploy must run `php artisan queue:restart`; Supervisor
  then starts a worker with the new code.
- `--max-time=3600` restarts the worker every hour, which frees memory. `--tries=3` applies to the notifications
  that do not set their own `$tries`.
- Both programs run as the web server user, so the files they write in `storage/` stay writable for PHP-FPM.

## First installation

1. Clone the repository and check out the release tag:

   ```bash
   git clone https://github.com/scify/Mentorship-matching-backend.git /var/www/jobpairs
   cd /var/www/jobpairs
   git checkout <tag>
   ```

2. Create `.env` from `.env.example` and set the values from [Production `.env`](#production-env).
3. Install the PHP dependencies and create the key:

   ```bash
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   php artisan key:generate --force
   ```

4. Build the frontend assets (devDependencies are needed for the build):

   ```bash
   npm ci
   npm run build
   ```

5. Create the tables and the lookup data (roles, statuses, specialties):

   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

   `db:seed` also creates staff accounts with a default password (`database/seeders/UserTableSeeder.php`).
   Change these passwords, or delete the accounts, before the site goes public.
6. Make `storage/` and `bootstrap/cache/` writable for the web server user.
7. Cache the configuration, routes and views:

   ```bash
   php artisan optimize
   ```

8. Configure nginx ([Web server](#web-server)) and Supervisor
   ([Supervisor](#supervisor-queue-worker-and-scheduler)).

## Update procedure

Read the [upgrade notes](#upgrade-notes) of every version between the installed one and the new one first. Some
steps must happen before the update.

1. Back up the database and `storage/app/` (it holds the uploaded CVs).
2. Put the app in maintenance mode:

   ```bash
   php artisan down
   ```

3. Check out the new release:

   ```bash
   git fetch --tags origin
   git checkout <tag>
   ```

4. Update `.env` if the upgrade notes ask for it. The build reads `VITE_*` values.
5. Install the PHP dependencies:

   ```bash
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   ```

6. Build the frontend assets, or copy `public/build/` from the build machine:

   ```bash
   npm ci
   npm run build
   ```

7. Run the migrations:

   ```bash
   php artisan migrate --force
   ```

8. Rebuild the caches. Clear first: an old route cache keeps routes that the new release removed.

   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

9. Restart the queue worker and reload PHP-FPM:

   ```bash
   php artisan queue:restart
   sudo systemctl reload php8.4-fpm
   ```

10. Run the "after the update" steps of the upgrade notes.
11. Leave maintenance mode:

    ```bash
    php artisan up
    ```

12. Check the result: log in as a staff user, then run `sudo supervisorctl status` (both programs `RUNNING`)
    and `php artisan schedule:list` (the two daily commands).

If a step fails, read `storage/logs/laravel.log`, fix the cause and run the procedure again from that step. Run
`php artisan up` only when the app works again.

## Upgrade notes

Each section lists what an operator must do for one version, in order. Changes that need no action are in the
CHANGELOG.

### Unreleased

This release hardens access control and the public pages, and moves the mail queue and the scheduler to
Supervisor.

**Before the update**

1. Back up the database and `storage/app/`.
2. Set up Supervisor ([Supervisor](#supervisor-queue-worker-and-scheduler)) and set `QUEUE_CONNECTION=database`.
   The scheduler no longer starts a queue worker, so without Supervisor no mail goes out. Remove any
   `schedule:run` cron entry for this app.
3. Set these `.env` values:
   - `APP_URL` = the public URL. The password reset links and the rating links are now built from it.
   - `APP_DEBUG=false`.
   - `SESSION_SECURE_COOKIE=true`.
   - `SENTRY_SQL_BINDINGS=false`, unless you want SQL values in Sentry.
4. Run these read-only checks:

   ```sql
   -- Stored links that are not http(s). The profile and company pages no longer show them as links.
   SELECT 'mentor' AS source, id, linkedin_url AS link FROM mentor_profile
     WHERE linkedin_url <> '' AND linkedin_url NOT REGEXP '^https?://'
   UNION ALL
   SELECT 'mentee', id, linkedin_url FROM mentee_profile
     WHERE linkedin_url <> '' AND linkedin_url NOT REGEXP '^https?://'
   UNION ALL
   SELECT 'company', id, website FROM company
     WHERE website <> '' AND website NOT REGEXP '^https?://';

   -- Staff accounts that can no longer use the backoffice: inactive, or without a role.
   SELECT u.id, u.email, u.state_id FROM users u
     WHERE u.deleted_at IS NULL
       AND (u.state_id <> 1
            OR NOT EXISTS (SELECT 1 FROM user_role r WHERE r.user_id = u.id AND r.deleted_at IS NULL));
   ```

   Correct a link in the edit form, or leave it: the page then shows it as text. Give a role to every account in
   the second list that still needs access.

**Update**

5. Follow the [update procedure](#update-procedure). `optimize:clear` is required: the release removes the
   `/register` page, the API route and the test routes.

**After the update**

6. Move the CVs that were uploaded before this release to private storage (safe to run again):

   ```bash
   php artisan cv:move-to-private
   ```

7. Remove the public storage link, which nothing uses any more:

   ```bash
   php artisan storage:unlink
   ```

**What users notice**

- Rating links in mails sent before this release no longer work (403). The new links are signed and valid for
  30 days.
- Account managers can change only their own sessions; administrators can change all sessions.
- The public registration forms accept 10 submissions per 10 minutes from one address. Logged-in staff have no
  limit.

## Troubleshooting

**Mails do not go out.** Run `sudo supervisorctl status`. If `jobpairs-worker` runs, list the failed jobs with
`php artisan queue:failed`, fix the cause (often the `MAIL_*` values) and retry them with
`php artisan queue:retry all`.

**A change in `.env` has no effect.** The configuration is cached. Run `php artisan optimize`, then
`php artisan queue:restart`.

**`ViteManifestNotFoundException`.** The frontend assets were not built or not copied. Run `npm ci` and
`npm run build`, or copy `public/build/`.

**Every page returns 503.** The app is still in maintenance mode. Run `php artisan up`.
