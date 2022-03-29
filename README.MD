## Description

It's a small PHP framework, which is built using some of [Symfony](https://symfony.com) components.
It consists of a framework itself and a [Docker](https://docker.com/) configuration.
Built in docker configuration is ready for usage (development purposes) and includes configured [Xdebug](https://xdebug.org) and [Composer](https://getcomposer.org/).
**Xdebug** is configured to use **9003** port and should work out of the box.

## Usage

For using built in **Docker** configuration consider installing **Docker** before (read [this article](https://www.docker.com/get-started/)).

If you wish to change web server port you can just set an environment variables **HTTP_PORT** and **HTTPS_PORT** before building a container.
To run a web server locally just use built in docker configuration
```
docker-compose up -d
```

After that install composer packages using **Composer** inside container or your local composer
```
composer install
```

Finally, application will be available on port **80** (if nothing were changed).

### Available built in routes
Currently, built in routes supports only **GET** method

+ /
+ /blog
+ /blog/{YYYY}/{MM}/{DD}

Where {YYYY} - year (4 digits), {MM} - month (2 digits), {DD} - day (2 digits)

### Implementing a new route

If you create a new controller outside of `src/Controller` folder don't forget to declare it as a service in `config/services.yaml`. 
Just create a controller and specify a route in `config/routes.yaml` following **[Symfony routing component rules](https://symfony.com/doc/5.4/routing.html)**.

### Running tests

After installing all the dependencies just run
```
vendor/bin/phpunit
```

Currently, there are only some Unit tests implemented

## External libraries used

All the external libraries used are specified in `composer.json`.
Basically this framework is built using these **Symfony** components:

+ Routing
+ Yaml
+ Dependency Injection
+ HTTP Foundation
