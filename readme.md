
# Mentorship matching backend platform


### Installing dependencies (assuming apache as web server and mysql as db):

In a nutshell (assuming debian-based OS), first install the dependencies needed:

Note: php package installs apache2 as a dependency so we have no need to add it manually.

Note: Please install the node and npm versions as listed below:
```bash
$ node -v
v6.17.1

$ npm -v
3.10.10
```

```
% sudo aptitude install php7.1 php7.1-cli mcrypt php7.1-mcrypt mysql-server php7.1-mysql
```

Install composer according to official instructions (link above) and move binary to ~/bin:
```% curl -sS https://getcomposer.org/installer | php5 && mv composer.phar ~/bin```

Download Laravel installer via composer:
```% composer global require "laravel/installer"```


And add ~/.composer/vendor/bin to your $PATH. Example:
```
% cat ~/.profile
[..snip..]
LARAVEL=/home/username/.composer/vendor
PATH=$PATH:$LARAVEL/bin
```
And source your .profile with % source ~/.profile

##Apache configuration:
```
% cat /etc/apache2/sites-available/mentorhsip-matching.conf
<VirtualHost *:80>
    ServerName dev.mentorhsip-matching
    DocumentRoot "/path/to/Mentorship-matching-backend/public"
    <Directory "/path/to/Mentorship-matching-backend/public">
        AllowOverride all
    </Directory>
</VirtualHost>
```
Make the symbolic link:
```
% cd /etc/apache2/sites-enabled && sudo ln -s ../sites-available/mentorhsip-matching.conf
```
Enable mod_rewrite and restart apache:
```
% sudo a2enmod rewrite && sudo service apache2 restart
```
Fix permissions for storage directory:
```
sudo chown -R user:www-data storage
chmod 775 storage
cd storage/
find . -type f -exec chmod 664 {} \;
find . -type d -exec chmod 775 {} \;
```
Test your setup with:
```
% php artisan serve
```
and navigate to localhost:8000.

## Setup DB
Laravel provides a simple yet powerful mechanism for creating the DB schema, called [Migrations](https://laravel.com/docs/5.5/migrations)
Simply run ```php artisan migrate``` to create the appropriate DB schema.

## Add seed data to DB
Run ```php artisan db:seed``` in order to insert the starter data to the DB by using [Laravel seeder](https://laravel.com/docs/5.5/seeding)

## Building project
After cloning the project, create an .env file (should be a copy of .env.example),
containing the information about your database name and credentials. 
After that, download all Laravel dependencies through [Composer](https://laravel.com/docs/5.5/installation), by running

```
composer install

composer update
```

After all Laravel dependencies have been downloaded, it's time to download all Javascript libraries and dependencies. 
We achieve that by using [npm](http://blog.npmjs.org/post/85484771375/how-to-install-npm).
Read [this](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-an-ubuntu-14-04-server) link in order to understand how npm should be installed.

If you prefer installing npm through [homebrew](http://brew.sh/) or [linuxbrew](http://linuxbrew.sh/), read [this](http://blog.teamtreehouse.com/install-node-js-npm-linux).

So, when in project root directory, and after npm has been installed correctly, run
```
npm install
```
To download and install all libraries and dependencies.

## Compiling assets
Make sure the following versions of Node and NPM are used in order to compile the front-end dependencies:
- Node Version: 6.14.4
- NPM Version: 3.10.10


This project uses [Elixir](https://laravel.com/docs/5.5/elixir) which is a tool built on [Gulp](http://gulpjs.com/),
a popular toolkit for automating painful or time-consuming tasks, like SASS compiling and js/css concatenation and minification.

To install gulp and gulp-cli (command line interface), please read [this](https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md).

Then, when in project root directory, run 
```gulp --local```
In order for the assets to compile. Also, by running
```gulp watch --local```
A watcher is set for when a file is changed to be compiled automatically.

## Deploying
You can run either  ```php artisan serve``` or set up a symbolic link to ```/path/to/project/public``` directory and navigate to http://localhost/{yourLinkName}


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

This project is open-sourced software licensed under the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).

## Credits

Icons used in this project are made by 
- [Freepik](http://www.flaticon.com/authors/freepik) from [www.flaticon.com](http://www.flaticon.com)
- [Vectors Market](http://www.flaticon.com/authors/vectors-market) from [www.flaticon.com](http://www.flaticon.com)
- [Prosymbols](http://www.flaticon.com/authors/prosymbols)  from [www.flaticon.com](http://www.flaticon.com)
