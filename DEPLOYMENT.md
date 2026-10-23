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
- [File ownership](#file-ownership)
- [First installation](#first-installation)
- [Update procedure](#update-procedure)
- [Upgrade notes](#upgrade-notes)
- [Troubleshooting](#troubleshooting)

## What runs on the server

| Part | What it does |
|---|---|
| nginx + PHP-FPM | Serves `public/`. |
| MySQL | The application database. |
| Supervisor: `jobpairs-worker` | Runs `php artisan queue:work`. Every app notification is queued (`QUEUE_CONNECTION=database`); the worker sends them. |
| Supervisor: `jobpairs-scheduler` | Runs `php artisan schedule:work`. It starts `mentees:notify-unmatched` and `email:follow-up` every day at midnight. |
| SMTP server | Receives the outgoing mail. |

Node.js is needed only to build the frontend assets. The build can run on the server or on a build machine that
copies `public/build/` to the server (the staging deployment in the private repository `scify/jobpairs-deploy`
builds on a CI runner).

## Requirements

- PHP 8.4 (`composer.json`) with the extensions that Laravel 13 needs, plus `pdo_mysql`. The PHP CLI also needs
  `pcntl`: the queue worker uses it for job timeouts and for a clean stop when Supervisor stops it.
- Composer 2.
- MySQL. Local development and CI use MariaDB 11.8, which is compatible for everything this app does.
- For the build: Node.js `^22.23.4`, `^24.22.1` or `^26.12.1`, and npm `>=11.19.1` (`package.json` `engines`).
  `.npmrc` sets `engine-strict`, so `npm ci` refuses older versions. `.nvmrc` pins 24.22.1.
- Supervisor.
- HTTPS. The session cookie is marked secure (see `SESSION_SECURE_COOKIE`).

## Production `.env`

Start from `.env.example`. These keys need a production value:

| Key | Production value | Why |
|---|---|---|
| `APP_ENV` | `production` (`staging` on staging) | |
| `APP_DEBUG` | `false` | `.env.example` ships `true`, which shows stack traces and configuration to visitors. |
| `APP_KEY` | generated once, then kept | A new key logs out every user and breaks the signed rating links in sent mails. |
| `APP_URL` | the public URL, for example `https://backoffice.example.org` | The queue worker has no request, so every link in a mail is built from it. The password reset link is built from it too. |
| `DB_*` | the production database | |
| `QUEUE_CONNECTION` | `database` | The Supervisor worker sends the mails. With `sync`, a slow mail server slows every request that sends mail. |
| `MAIL_*` | the SMTP server and sender | |
| `SESSION_SECURE_COOKIE` | `true` | The browser sends the session cookie over HTTPS only. |
| `LOG_LEVEL` | `warning` or `error` | `.env.example` has `debug`, which logs every detail. |
| `SENTRY_LARAVEL_DSN` | the project DSN, or empty | Empty disables Sentry. |
| `SENTRY_SQL_BINDINGS` | `false` | `true` sends SQL values, including personal data, to Sentry. |
| `VITE_SENTRY_DSN_PUBLIC` | the public DSN, or empty | Read at build time: set it before `npm run build`. |

The app caches its configuration (`php artisan optimize`). After every change to `.env`, run
`php artisan optimize` and `php artisan queue:restart`; otherwise PHP-FPM and the worker keep the old values.

## Web server

- The document root is `public/`. Nothing above `public/` may be served.
- Pin `server_name` to the public host name, and reject other hosts with a default server.
- Behind Cloudflare, set the visitor address in nginx (`set_real_ip_from` + `real_ip_header CF-Connecting-IP`; on
  SciFY servers this is `/etc/nginx/conf.d/cloudflare-realip.conf`). The app has no trusted proxy list. Without
  this setting, the request throttles count all visitors as one client.
- CV uploads may be up to 10 MB (`max:10000` in the mentor and mentee forms). Allow them: nginx
  `client_max_body_size 12m;`, PHP `upload_max_filesize = 10M` and `post_max_size = 12M`. The defaults (1 MB in
  nginx, 2 MB in PHP) reject most CVs.
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
- The worker keeps the application code and configuration in memory. Every deploy and every `.env` change must
  run `php artisan queue:restart`; Supervisor then starts a worker with the new code.
- `--max-time=3600` restarts the worker every hour, which frees memory. `--tries=3` applies to the notifications
  that do not set their own `$tries`.

## File ownership

PHP-FPM and both Supervisor programs run as `www-data` and write to `storage/` and `bootstrap/cache/`: uploaded
CVs, logs (one file per day), the file cache, sessions and compiled views. A file that another user creates
there, for example root, can later make uploads or logging fail.

Run every `git`, `composer`, `npm` and `php artisan` command of the installation and the update as `www-data`:

```bash
sudo -u www-data php artisan migrate --force
```

Only `systemctl` and `supervisorctl` need root.

The alternative is a deploy user in the `www-data` group with group-writable directories. The staging deployment
does this: after each deploy it runs `set-laravel-permissions-ci.sh`, which sets 775/664 modes on `storage/` and
`bootstrap/cache/`, gives the deploy user's files there to the `www-data` group and sets the setgid bit, so new
files inherit the group.

If a command ran as another user by mistake, give the files back:

```bash
sudo chown -R www-data:www-data /var/www/jobpairs/storage /var/www/jobpairs/bootstrap/cache
```

## First installation

Run the commands as `www-data` ([File ownership](#file-ownership)).

1. Clone the repository and check out the release:

   ```bash
   git clone https://github.com/scify/Mentorship-matching-backend.git /var/www/jobpairs
   cd /var/www/jobpairs
   git checkout <release>
   ```

   `<release>` is a release tag. Until the first tag exists, use a commit of `master`.
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
6. Cache the configuration, routes and views:

   ```bash
   php artisan optimize
   ```

7. Configure nginx and PHP ([Web server](#web-server)) and Supervisor
   ([Supervisor](#supervisor-queue-worker-and-scheduler)).

## Update procedure

Read the [upgrade notes](#upgrade-notes) of every version between the installed one and the new one first. Some
steps must happen before the update. Run the commands as `www-data` ([File ownership](#file-ownership)).

1. Back up the database and `storage/app/` (it holds the uploaded CVs).
2. Stop the scheduler, so that it does not boot half-updated code, and put the app in maintenance mode:

   ```bash
   sudo supervisorctl stop jobpairs-scheduler
   php artisan down --render="errors::503"
   ```

   `--render` prerenders the 503 page. `public/index.php` then answers every request before it loads `vendor/`,
   which changes in the next steps. The queue worker pauses by itself in maintenance mode.
3. Clear the caches of the installed version, while its code is still in place. Otherwise `composer install` and
   `migrate` boot the new code with the old cached configuration and routes.

   ```bash
   php artisan optimize:clear
   ```

4. Check out the new release:

   ```bash
   git fetch --tags origin
   git checkout <release>
   ```

5. Update `.env` if the upgrade notes ask for it. The build reads `VITE_*` values.
6. Install the PHP dependencies:

   ```bash
   composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
   ```

7. Build the frontend assets, or copy `public/build/` from the build machine:

   ```bash
   npm ci
   npm run build
   ```

8. Run the migrations:

   ```bash
   php artisan migrate --force
   ```

9. Cache the configuration, routes and views. From here on the new `.env` values apply.

   ```bash
   php artisan optimize
   ```

10. Restart the queue worker, reload PHP-FPM and start the scheduler:

    ```bash
    php artisan queue:restart
    sudo systemctl reload php8.4-fpm
    sudo supervisorctl start jobpairs-scheduler
    ```

11. Run the "after the update" steps of the upgrade notes.
12. Leave maintenance mode:

    ```bash
    php artisan up
    ```

13. Check the result:
    - Log in as a staff user.
    - `sudo supervisorctl status` shows both programs `RUNNING`.
    - `php artisan schedule:list` shows the two daily commands.
    - A few minutes later, `SELECT COUNT(*) FROM jobs;` is 0 or falls. A growing count means that the worker
      does not run.

If a step fails, read the newest `storage/logs/laravel-*.log`, fix the cause and continue from that step. Run
`php artisan up` only when the app works again.

## Upgrade notes

Each section lists what an operator must do for one version, in order. The CHANGELOG (from the first tagged
release) lists the changes that need no action.

### Unreleased

This release hardens access control and the public pages, and moves the mail queue and the scheduler to
Supervisor. If the server runs code from before 2026-08-15, the release also upgrades Laravel to 13 and PHP to
8.4; code from before 2026-09-22 also moves the asset build from Laravel Mix to Vite.

**Before the update**

1. Back up the database and `storage/app/`.
2. Check the runtimes against [Requirements](#requirements): PHP 8.4 with `pcntl` for the CLI, and on the build
   machine Node.js `>=24.22.1` (or `^22.23.4`, `^26.12.1`) with npm `>=11.19.1`. Set the upload limits from
   [Web server](#web-server).
3. Set up Supervisor ([Supervisor](#supervisor-queue-worker-and-scheduler)) and set `QUEUE_CONNECTION=database`.
   The scheduler no longer starts a queue worker, so without Supervisor the mails wait in the `jobs` table.
   Remove any `schedule:run` cron entry for this app.
4. Set these `.env` values. They apply from step 9 of the update procedure, when the configuration is cached
   again.
   - `APP_URL` = the public URL. The password reset links and all links in mails are built from it.
   - `APP_DEBUG=false`.
   - `SESSION_SECURE_COOKIE=true`.
   - `LOG_LEVEL=warning`.
   - `SENTRY_SQL_BINDINGS=false`, unless you want SQL values in Sentry.
5. Run these read-only checks:

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

6. Follow the [update procedure](#update-procedure).

**After the update**

7. Move the CVs that were uploaded before this release to private storage (safe to run again):

   ```bash
   php artisan cv:move-to-private
   ```

8. Remove the public storage link, which nothing uses any more:

   ```bash
   php artisan storage:unlink
   ```

**What users notice**

- Rating links in mails sent before this release no longer work (403). The new links are signed and valid for
  30 days.
- Account managers can change only their own sessions; administrators can change all sessions.
- The public registration forms accept 10 submissions per 10 minutes from one address. Logged-in staff have no
  limit.
- The `/register` page is gone. Administrators create staff accounts.

## Troubleshooting

**Mails do not go out.** Run `sudo supervisorctl status`. If `jobpairs-worker` runs, list the failed jobs with
`php artisan queue:failed`, fix the cause (often the `MAIL_*` values) and retry them with
`php artisan queue:retry all`.

**A change in `.env` has no effect.** The configuration is cached. Run `php artisan optimize`, then
`php artisan queue:restart`.

**CV uploads fail.** Check the upload limits ([Web server](#web-server)) and the owner of
`storage/app/private/cv_files` ([File ownership](#file-ownership)).

**`ViteManifestNotFoundException`.** The frontend assets were not built or not copied. Run `npm ci` and
`npm run build`, or copy `public/build/`.

**Every page returns 503.** The app is still in maintenance mode. Run `php artisan up`.
