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

### Server login
For SAML authentication work, your machine name (sc-c3XX-pcXX.cpnv.ch) must be referenced in the SAML server of the intranet. You need to put this on the SAML server :
```
$metadata['http://sc-c3XX-pcXX.cpnv.ch/saml2/metadata'] = array(
    'AssertionConsumerService' => 'http://sc-c3XX-pcXX.cpnv.ch/saml2/acs',
    'SingleLogoutService' => 'http://sc-c3XX-pcXX.cpnv.ch/saml2/sls',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    'simplesaml.nameidattribute' => 'uid'
);
```

On your side, if you use Homestead, you must redirect host port 80 to virtual port 80 (in the homestead.yaml file) and in the.env file, you must change the parameter as such: HOST_URL=http://sc-c3XX-pcXX.cpnv.ch

After that, you can login in GPNV with your intranet account.

### Local login
If you want to use GPNV without SAML, in.env, you must define `USAGE=LOCAL`. The application will automatically log in with the first account in the database.

## Deployment
> Note: make sure you have the credentials to access the different services

The website is hosted on swisscenter center.
Here is the link for the managing panel: https://apanel.swisscenter.com/login

The web server does not have any cli, so a `composer install` or `composer update`
to make sure that all the dependencies are installed.
Once it's done, that the '.htaccess' file in root and 'public' folder have to
be edited. The lines which start with 'php_value' must be remove.

Also make sure that the .env file has the right database credentials (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, HOST_URL)

When all those things are done, the website can be upload on the server by ftp.

## Credits
Web developers :
 - GIORDANO Antonio
 - JORDIL Kevin
 - RICHOZ Julien
 - BAZZARI Raphaël
 - MARCOUP Thomas
 - SILVA-MARQUES Fabio-Manuel

Client :
 - CARREL Xavier
