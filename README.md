# klusbib-module

This project is intended to be used as [Snipe-IT](https://snipeitapp.com/) extension module.
It provides extra functionality for tool libraries like [Klusbib](https://www.klusbib.be)

## Installing
Install modules dependency

    composer require nwidart/laravel-modules

Optionally, install module installer to copy modules in 'Modules' directory

    composer require joshbrw/laravel-module-installer

Add this repository to snipe-it composer.json

    "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/renardeau/klusbib-module"
        }
      ],

Add this module as required dependency

    "require": {
          "renardeau/klusbib-module": "dev-master"
        }

Run  
```composer update```


## Deploying

### Dokku

```
dokku apps:create <app-name>
dokku mysql:create <app-name>db
dokku mysql:link <app-name>db <app-name>
dokku storage:mount <app-name> /var/lib/dokku/data/storage/<app-name>/storage:/app/storage
dokku storage:mount <app-name> /var/lib/dokku/data/storage/<app-name>/public/uploads:/app/public/uploads
```

*IMPORTANT: create directory structure under storage and uploads*

Make sure to **not** specify the BUILDPACK_URL env variable in order to let the .buildpacks file indicate the appropriate buildpacks  

**Database settings**  
```
DB_CONNECTION=mysql
DB_HOST=<from mysql service>
DB_DATABASE=<from mysql service>
DB_USERNAME=mysql
DB_PASSWORD=<from mysql service>
DB_PREFIX=null
DB_DUMP_PATH='/app/.apt/usr/bin'
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```
For backups, the application requires mysqldump. Add mysql-client to Aptfile and update DB_DUMP_PATH to '/app/.apt/usr/bin'

Install Laravel passport keys
```
php artisan passport:install
```

**Email settings**  
*Setup with mailtrap*  
For a test environment, you can setup mailtrap as email server to catch and review all emails sent by application  
* Create mailtrap account on https://mailtrap.io/
* Lookup credentials in settings of your Inbox
* Update credentials in .env file
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=<from mailtrap Inbox credentials>
MAIL_PASSWORD=<from mailtrap Inbox credentials>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDR=info@klusbib.be
MAIL_FROM_NAME='Klusbib Team'
MAIL_REPLYTO_ADDR=info@klusbib.be
MAIL_REPLYTO_NAME='Klusbib Team'
MAIL_BACKUP_NOTIFICATION_ADDRESS=admin@deelbaarmechelen.be
```

**Logging settings**  
To create daily log files, update .env file:
```
APP_LOG=daily
APP_LOG_MAX_FILES=10
APP_LOG_LEVEL=debug
```

**Generate APP_KEY**  
To generate the APP_KEY, a .env file needs to exist in /app directory. Dokku, however, overwrites the default .env file...  
The workaround is to persist the .env file by copying it to the storage directory (which is a mounted volume - see above) and restore it from there before running the ```php artisan key:generate``` command

**Connection with Klusbib API**  
Extra configuration settings are required for the Klusbib module to interact with API in .env file
```
# ---------------------------------------------
# KLUSBIB
# ---------------------------------------------
KLUSBIB_API_URL=https://api.klusbib.be
KLUSBIB_API_USER=<your user>
KLUSBIB_API_PASSWORD=<your password>
```

The Klusbib API will also need an API key to enable API-Inventory synchronisation

Seeding data:
```
php artisan module:seed Klusbib
```


**Update of app configuration from .env file**  
Convert a .env file into dokku config:set command:  
```
# read the lines of your .env file, skips blank lines and comments, and print the dokku command
cat .env | awk '/^\s*$/ {next;} !/^#/ {print "dokku config:set --no-restart <app-name>", $0}' > update_app_config.sh

# run the generated dokku commands
. update_app_config.sh

# restart app
dokku ps:restart <app-name>
```

Browse to webpage to start pre-flight checks.  
In case of failure at database setup, it might be needed to manually restart database migration from app command line  
```
# enter app
dokku enter <app-name>

# relaunch database migration
php artisan migrate
```

**Enable SSL (https)**

Enabling SSL with letsencrypt on dokku:  
```
dokku config:set --no-restart <app-name> DOKKU_LETSENCRYPT_EMAIL=<your-email>
dokku letsencrypt <app-name>
dokku config:set <app-name> APP_ENV=production
```

Forcing https for all routes
```
// web.php

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}
```

**Backup and restore**

Backup database
```
 dokku mysql:export inventorydb > /tmp/inventorydb.sql
```

Restore database
```
 dokku mysql:import inventorydb < /tmp/inventorydb.sql
```
 

## Troubleshooting
* **Problem**:  
*connect() failed (111: Connection refused) while connecting to upstream*  
When this error shows up in log of nginx in dokku, it indicates the requests can not be forwarded to the app.  
Check the port settings of your app are correctly mapped and the web server is running

```
dokku@dokku-deelbaarm:~/testinventory$ dokku proxy:ports testinventory
-----> Port mappings for testinventory
    -----> scheme  host port  container port
    http           80         5000
dokku@dokku-deelbaarm:~/testinventory$
```

Note: port mappings can be influenced by *DOKKU_PROXY_PORT_MAP* env setting. Remove it with  
```dokku config:unset testinventory DOKKU_PROXY_PORT_MAP```

* **Problem**:  
*Missing mbstring extension*  
Solved by adding Aptfile to install libonig-dev and libonig4 packages and .buildpacks file to force usage of apt and php buildpack:
```
https://github.com/heroku/heroku-buildpack-apt
https://github.com/heroku/heroku-buildpack-php#v187
```
The php buildpack is forced to v187 to make use of composer 2.0, but still allow install of PHP 7.1. An upgrade to a more recent PHP version should be planned together with the upgrade of snipe IT

* **Problem**:  
*PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test-inventorydb.oauth_clients' doesn't exist in /app/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOConnection.php:61*  
Missing install of passport?  
Repeat php artisan migrate command on app:  
```
dokku enter <app-name>
php artisan migrate
php artisan passport:install      # to install passport keys
```

* **Problem**:  
*No CSS or js available*  
-> was due to incorrect Rewrite directives in vhost definition. These can be simply removed as already covered by .htaccess files. 
The Rewrite directive in .htaccess allow to rewrite urls, but still provide direct access to resources in e.g. public/js dir
See also https://snipe-it.readme.io/docs/linuxosx (this piece of info is missing on windows setup instructions)
Also make sure to set AllowOverride to all to allow the override by htaccess

* **Problem**:  
*Database migration failing due to 'SQLSTATE[42000]: Syntax error or access violation: 1067 Invalid default value for 'labels_width' (SQL: alter table settings add labels_per_page tinyint not null default '30', add labels_width decimal(6, 5) not null default '2,625''*  
-> locale issue causing to reject the default value (use of , instead of .?). Workaround: set APP_LOCALE=en in .env (was nl on my setup) and retry migration. See https://github.com/snipe/snipe-it/issues/4002

* **Problem**:  
*Unable to list users or assets after deploy*  
The API calls are failing with response code 401 (Unauthorized). The root cause seems to be an update of composer/framework dependency from 5.5.49 to 5.5.50.   
Changes are listed here https://github.com/laravel/framework/compare/v5.5.49...v5.5.50  
The issue might be related to a cookie security fix (see https://blog.laravel.com/laravel-cookie-security-releases)  
The applied workaround is to force use of 5.5.49 in composer.json  
Note that latest version of snipe upgraded to laravel/framework 6.x and no longer has this issue, so the long term solution is to upgrade snipe IT

* **Problem**:  
*Trying to get property of non-object at /app/vendor/laravel/passport/src/ClientRepository.php:81)*  
Install Laravel Passport keys with 
```
dokku enter <app-name>
php artisan passport:install
```


* **Problem**:  
*ErrorException: Replicating claims as headers is deprecated and will removed from v4.0. Please manually set the header if you need it replicated. in /app/vendor/lcobucci/jwt/src/Builder.php:334*  
Downgrade jwt package to version 3.3.3
```
composer require lcobucci/jwt=3.3.3
```

* **Problem**:  
*Command not found : sh: 1: /usr/bin/mysqldump: not found*  
Update the DB_DUMP_PATH environment variable  
Make sure mysqldump is installed (eventually add the mysql-client package with apt)  
See also https://github.com/spatie/db-dumper
