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

Run ```composer update```
