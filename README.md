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
To use **GPNV** without login via SAML, you simply need to add this line in **.env** file :

```
LOCAL_USER={id}
```

If you don't specify any id, it won't change default login behavior. You still need to logout manually if the setting is removed.

## Capistrano Deployment 
> Note: make sure you have the credentials to access the different services, and the right credentials in the .env file (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, HOST_URL)

The website is hosted on swisscenter center.
Here is the link for the managing panel: https://apanel.swisscenter.com/login

### Requirement
- Ruby installed: `choco install ruby`
- Capistrano installed: `gem install capistrano capistrano-laravel`
- Add Public/Private key according to the file `CapDeploy`in the config folder
- The GPNV project is already capified, so you don't need to `cap install`
### Deployment
Everytime you need to deploy your applcation:
1. Go on Swisscenter and activate the SSH access
2. Type in your project `cap production deploy`
- If problem with version lock at "x.x.x", use `bundle exec cap production deploy` or go in `config/deploy.rb` and change the version to the one you use

## Credits
Web developers :
 - GIORDANO Antonio
 - JORDIL Kevin
 - RICHOZ Julien
 - BAZZARI Raphaël
 - MARCOUP Thomas
 - SILVA-MARQUES Fabio-Manuel
 - NEVES Quentin
 - BAUMANN Philippe

Client :
 - CARREL Xavier
