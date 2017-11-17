# OX-Archive

Ce projet a été realiser par des étudants de l' Université de La Rochelle pour répondre à une demande de OpenXtrem

## Getting Started
### Apache 2.4 configuration
```
<VirtualHost *:80>
    DocumentRoot "/var/www/html/OX-Archive/web"
    DirectoryIndex index.php
    ServerName ox-archive

    Alias /web/ /var/www/html/OX-Archive/web/
    <Directory "/var/www/html/OX-Archive/web">
        AllowOverride All
        FallbackResource /web/index.php
    </Directory>
</VirtualHost>

```
In /etc/hosts define a dns for your website, i.e ox-archive in this case
### Prerequisites

* Apache
* php
* sqlite
* elasticsearsh

### Installing

clone or download then use composer install
create the archives directory where you want to put it.
config the application in app.php and extra config in the config directory
create database structure and store some users for test purpose
```
git clone git@github.com:AdriwanKenoby/OX-Archive.git
cd OX-Archive
composer install
sqlite3 app/app.db < db/structure.sql
sqlite3 app/app.db < db/content.sql
```
sea the content of content.sql to find credentials

you can generate FHIR classes but it is not use for instance in the project

```
php bin/console archive:fhir:generate-classes
```

End with an example of getting some data out of the system or using it for a little demo

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests
Explain what these tests test and why

```
Give an example
```


## Deployment

You have to install content of mediboard_side directory on a module, see
* [Medibaord](http://mediboard.org/) - for more info
* You can fully configure environnement in app.php

## Built With

* [Silex](https://silex.symfony.com/) - The web framework used
* [Composer](https://getcomposer.org/) - Dependency Management

## Authors

* **Véteau Adrien** - *Initial work* - [AdriwanKenoby](https://github.com/AdriwanKenoby)
* **Romain Badens** - *Initial work* - [FrOOmiX](https://github.com/FrOOmiX)
* **Steve Dechaume** - *Initial work*
## License

* @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
* @license    The Engine kernel use to parse document in elasticsearch is licensed under the CeCILL-B_V1 License - see the [CeCILL-B_V1](http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.htmlThis) file for details
