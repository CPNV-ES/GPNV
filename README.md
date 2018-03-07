# GPNV

## Requirements
- Laravel 5.5
- Composer
- PHP >= 7.0.0

## Installation
> Note: This procedure explain only how to set up the site, not how to configure
> Laravel or create a server. If you want, you can use vagrant/homestead here is the link
> with the instructions: https://laravel.com/docs/5.5/homestead#installation-and-setup
> Also note that the main web site is the "public" folder of the project not the root

Once this project is copy in your server, go to the project root and
use the following command :`cp .env.example .env`.
Don't forget to create the database and grant access to the user.

Create the database by default the name is `GPNV`
Grant access to the user by default `root` with password `root`
> Eventually, you can open the file `.env` and change the database name, password, etc.
> depends on your needs.

Composer is require for this project in order to install the project dependencies.
If you wan to install it, please follow the instructions on : https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
Once it's installed, or hardly installed, use the command : `composer install`

After that, use the command : `php artisan key:generate` in order to generate
the APP_KEY for the project.

And finally, use the command : `php artisan migrate` and `php artisan db:seed`
for generating the database structure and adding the test data.

>If you are on Linux os make sure that laravel has the necessary rights on the website directory

## Configuration Notes
These are the default logins information, you don't need that if you haven't
use the command : `php artisan db:seed` during the installation.

|        Login          | friendlyId |  role   |
| --------------------- | ---------- | ------- |
| utilisateur@mail.com  |      1     | student |
| tournesol@mail.com    |      2     | teacher |
| anno@nimme.com        |      3     | student |

## Use app in "Test Mode"
In order to use the site, an add-on able to modify HTTP Header is needed.
Here is some add-ons for the most used browsers :
 - Mozilla Firefox : https://addons.mozilla.org/fr/firefox/addon/modify-headers/?src=search
 - Google Chrome : https://chrome.google.com/webstore/detail/modify-headers-for-google/innpjfdalfhpcoinfnehdnbkglpmogdi

## Deployment
> Note: make sure you have the credentials to access the different services

The website is hosted on swisscenter center.
Here is the link for the managing panel: https://apanel.swisscenter.com/login

The web server does not have any cli, so a `composer install` or `composer update`
to make sure that all the dependencies are installed.
Once it's done, that the '.htaccess' file in root and 'public' folder have to
be edited. The lines which start with 'php_value' must be remove.

Also make sure that the .env file has the right database credentials (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD )

When all those things are done, the website can be upload on the server by ftp.

## Credits
Web developers :
 - BAZZARI Raphaël
 - MARCOUP Thomas
 - SILVA-MARQUES Fabio-Manuel

Client :
 - CARREL Xavier
