# Mentorship matching backend platform


###Installing dependencies (assuming apache as web server and mysql as db):

In a nutshell (assuming debian-based OS), first install the dependencies needed:

Note: php5 package installs apache2 as a dependency so we have no need to add it manually.

```
% sudo aptitude install php5 php5-cli mcrypt php5-mcrypt mysql-server php5-mysql
```

Install composer according to official instructions (link above) and move binary to ~/bin:
```% curl -sS https://getcomposer.org/installer | php5 && mv composer.phar ~/bin```

Download Laravel installer via composer:
```% composer global require "laravel/installer=~1.1"```


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
% cat /etc/apache2/sites-available/mysite.conf
<VirtualHost *:80>
    ServerName myapp.localhost.com
    DocumentRoot "/path/to/VoluntEasy/VoluntEasy/public"
    <Directory "/path/to/VoluntEasy/VoluntEasy/public">
        AllowOverride all
    </Directory>
</VirtualHost>
```
Make the symbolic link:
```
% cd /etc/apache2/sites-enabled && sudo ln -s ../sites-available/mysite.conf
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
Laravel provides a simple yet powerful mechanism for creating the DB schema, called [Migrations](https://laravel.com/docs/5.3/migrations)
Simply run ```php artisan migrate``` to create the appropriate DB schema.

## Add seed data to DB
Run ```php artisan db:seed``` in order to insert the starter data to the DB by using [Laravel seeder](https://laravel.com/docs/5.3/seeding)

## Building project
After cloning the project, create an .env file (should be a copy of .env.example),
containing the information about your database name and credentials. 
After that, download all Laravel dependencies through [Composer](https://laravel.com/docs/5.3/installation), by running

```
composer install

composer update
```

After all Laravel dependencies have been downloaded, it's time to download all Javascript libraries and dependencies. 
We achieve that by using [npm](http://blog.npmjs.org/post/85484771375/how-to-install-npm).
Read [this](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-an-ubuntu-14-04-server) link in order to understand how npm should be installed
If you prefer installing npm through [homebrew](http://brew.sh/) or [linuxbrew](http://linuxbrew.sh/), read [this](http://blog.teamtreehouse.com/install-node-js-npm-linux)
So, when in project root directory, and after npm has been installed correctly, run
```
npm install
```
To download and install all libraries and dependencies.

## Compiling assets

This project uses [Elixir](https://laravel.com/docs/5.3/elixir) which is a tool built on [Gulp](http://gulpjs.com/),
a popular toolkit for automating painful or time-consuming tasks, like SASS compiling and js/css concatenation and minification.


## Deploying
You can run either  ```php artisan serve``` or set up a symbolic link to ```/path/to/project/public``` directory and navigate to http://localhost/{yourLinkName}


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

This project is open-sourced software licensed under the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).
