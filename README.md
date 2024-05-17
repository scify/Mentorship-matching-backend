# Mentorship matching backend platform

This [Laravel](https://laravel.com/docs/8.x/) application is a platform backoffice that manages the matching process
between mentors with mentees, based on their
preferences and skills.

## First time install

After cloning the project, create an `.env` file (should be a copy of `.env.example`),
containing the information about your database name and credentials.

```bash
cp .env.example .env
```

## Docker option (recommended)

You can use the `docker-compose.yml` file that exists at project roo, to quickly set up a docker container.

To build the docker container, run:

```bash
docker compose build
```

And then run

```bash
docker compose up
```

To fire up the container.

Then, you can enter the container by running

```bash
docker exec -it mentorship_matching_platform_server bash
```

And from there, you can run all the `php artisan`, `composer`, and `npm` commands.

## Non-Docker option

### Installing dependencies (assuming apache as web server and mysql as db):

In a nutshell (assuming debian-based OS), first install the dependencies needed:

#### Frontend dependencies

Note: Please install the node and npm versions as listed below:

```bash
$ node -v
v14.21.3

$ npm -v
6.14.18
```

If using NVM, you can install the correct versions by running:

```bash
nvm use # reads the .nvmrc file and installs the correct node and npm versions
```

#### Backend dependencies

Note: php package installs apache2 as a dependency so we have no need to add it manually.

```bash
sudo aptitude install php7.4 php7.4-cli mcrypt php7.4-mcrypt mysql-server php7.4-mysql
```

#### Composer installation

Install composer globally by following the instructions [here](https://getcomposer.org/download/).

#### Apache configuration:

Edit the `/etc/apache2/sites-available/mentorhsip-matching.conf` so that it looks like:

```text
<VirtualHost *:80>
    ServerName dev.mentorhsip-matching
    DocumentRoot "/path/to/Mentorship-matching-backend/public"
    <Directory "/path/to/Mentorship-matching-backend/public">
        AllowOverride all
    </Directory>
</VirtualHost>
```

Make the symbolic link:

```bash
cd /etc/apache2/sites-enabled && sudo ln -s ../sites-available/mentorhsip-matching.conf
```

Enable `mod_rewrite` and restart the server:

```bash
sudo a2enmod rewrite && sudo service apache2 restart
```

Fix permissions for storage directory:

```bash
sudo chown -R user:www-data storage
chmod 775 storage
cd storage/
find . -type f -exec chmod 664 {} \;
find . -type d -exec chmod 775 {} \;
```

Test the setup by navigating to `http://dev.mentorhsip-matching` in your browser.

#### Laravel local server

You can also test your setup by running the Laravel local server:

```bash
php artisan serve
```

and navigating to [localhost:8000](http://localhost:8000).

## Setup the Database

Laravel provides a simple yet powerful mechanism for creating the DB schema,
called [Migrations](https://laravel.com/docs/6.0/migrations)
Simply run ```php artisan migrate``` to create the appropriate DB schema.

### Add seed data to DB

Run ```php artisan db:seed``` in order to insert the starter data to the DB by
using [Laravel seeder](https://laravel.com/docs/6.0/seeding)

## Building the project

Download all Laravel dependencies through [Composer](https://laravel.com/docs/6.0/installation), by running

```bash
composer install

composer update
```

After all Laravel dependencies have been downloaded, it's time to download all Javascript libraries and dependencies.
We achieve that by using [npm](http://blog.npmjs.org/post/85484771375/how-to-install-npm).
Read [this](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-an-ubuntu-14-04-server) link in
order to understand how npm should be installed.

If you prefer installing npm through [homebrew](http://brew.sh/) or [linuxbrew](http://linuxbrew.sh/),
read [this](http://blog.teamtreehouse.com/install-node-js-npm-linux).

So, when in project root directory, and after npm has been installed correctly, run

```bash
npm install
```

To download and install all libraries and dependencies.

## Compiling assets

When in project root directory, run

```bash
npm run dev
```

Or any other `npm` script that you want to run. The available scripts are listed in the `package.json` file.

## Deploying

You can run either  ```php artisan serve``` or set up a symbolic link to ```/path/to/project/public``` directory and
navigate to http://localhost/{yourLinkName}

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com.
All security vulnerabilities will be promptly addressed.

## License

This project is open-sourced software licensed under
the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).

## Credits

Icons used in this project are made by

- [Freepik](http://www.flaticon.com/authors/freepik) from [www.flaticon.com](http://www.flaticon.com)
- [Vectors Market](http://www.flaticon.com/authors/vectors-market) from [www.flaticon.com](http://www.flaticon.com)
- [Prosymbols](http://www.flaticon.com/authors/prosymbols)  from [www.flaticon.com](http://www.flaticon.com)
