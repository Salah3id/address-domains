# Laravel-Domains

[![Latest Version on Packagist](https://img.shields.io/packagist/v/salah3id/address-domains.svg?style=flat-square)](https://packagist.org/packages/salah3id/address-domains)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/salah3id/address-domains.svg?maxAge=86400&style=flat-square)](https://scrutinizer-ci.com/g/salah3id/address-domains/?branch=main)
[![Quality Score](https://img.shields.io/scrutinizer/g/salah3id/address-domains.svg?style=flat-square)](https://scrutinizer-ci.com/g/Salah3id/address-domains)
[![Total Downloads](https://img.shields.io/packagist/dt/salah3id/address-domains.svg?style=flat-square)](https://packagist.org/packages/salah3id/address-domains)

| **Laravel**  |  **address-domains** |
|---|---|
| 9.0  | ^1.0 |

`salah3id/address-domains` is a Laravel package which created to manage your large Laravel app using domains with repository design pattern to abstract the data layer, making our application more flexible to maintain. Domain is like a Laravel package, it has some views, controllers or models. This package is supported and tested in Laravel 9.

This package is a re-published, re-organised of [salah3id/laravel-modules](https://github.com/salah3id/laravel-modules), which isn't support repository design pattern.


## Install

To install through Composer, by run the following command:

``` bash
composer require salah3id/address-domains
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

``` bash
php artisan vendor:publish --provider="Salah3id\Domains\LaravelDomainsServiceProvider"
```

### Autoloading

By default, the domain classes are not loaded automatically. You can autoload your domains using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Domains\\": "Domains/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
  }

}
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**



# Creating a Domain

Generate your first domain using : 
```bash
  php artisan domain:make Address
```
The following structure will be generated.

    app/
    bootstrap/
    vendor/
    Domains/
    │   ├── Address/             
    │   │   ├── Assets/             
    │   │   ├── Config/             
    │   │   ├── Console/             
    │   │   ├── Database/
    │   │   │   ├── Migrations/ 
    │   │   │   ├── Seeders/
    │   │   ├── Entities/
    │   │   ├── Http/
    │   │   │   ├── Controllers/
    │   │   │   ├── Middleware/
    │   │   │   ├── Requests/
    │   │   ├── Providers/
    │   │   │   ├── AddressServiceProvider.php
    │   │   │   ├── RepositoryServiceProvider.php
    │   │   │   ├── RouteServiceProvider.php
    │   │   ├── Resources/
    │   │   │   │   ├── assets/
    │   │   │   │   │   ├── js/
    │   │   │   │   │   │   ├── app.js
    │   │   │   │   │   ├── sass/
    │   │   │   │   │   │   ├── app.scss
    │   │   │   │   ├── lang/
    │   │   │   │   ├── views/
    │   │   ├── Routes/
    │   │   │   ├── api.php
    │   │   │   ├── web.php
    │   │   ├── Repositories/
    │   │   ├── Tests/ 
    │   │   ├── composer.json
    │   │   ├── module.json
    │   │   ├── package.json
    │   │   ├── webpack.mix.js     
    │   │   └──
    │   └── Other Domains ...                    
    └── ... etc 

Generate multiple domains using : 
```bash
  php artisan domain:make Address User Admin Blog
```

### `domain:make` command options 

| Parameter |  Description                |
| :-------- |  :------------------------- |
| `--plain` , `-p` |  By default when you create a new domain, the command will add some resources like a controller, seed class, service provider, etc. automatically. If you don't want these, you can add --plain flag, to generate a plain domain. |

### Naming convention
Because we are autoloading the modules using [psr-4](http://www.php-fig.org/psr/psr-4/), we strongly recommend using StudlyCase convention.


## Utility commands
### domain:make 
Generate a new domain

```bash
  php artisan domain:make Address
```



### domain:make 
Generate multiple domains at once.

```bash
  php artisan domain:make Address User Admin 
```

### domain:use
This allows you to not specify the doamin name on other commands requiring the module name as an argument.

```bash
  php artisan domain:use Address 
```

### domain:unuse 
This unsets the specified domain that was set with the `domain:use` command.

```bash
  php artisan domain:unuse Address 
```

### domain:list 
List all available domains.

```bash
  php artisan domain:list 
```

### domain:migrate 
Migrate the given domain, or without a domain an argument, migrate all domains.

```bash
  php artisan domain:migrate Address
```

### domain:migrate-rollback 
Rollback the given domain, or without an argument, rollback all domains.

```bash
  php artisan domain:migrate-rollback Address
```

### domain:migrate-refresh 
 Refresh the migration for the given module, or without a specified module refresh all modules migrations.

```bash
  php artisan domain:migrate-refresh Address
```

### domain:migrate-reset 
Reset the migration for the given domain, or without a specified domain reset all domains migrations.
```bash
php artisan domain:migrate-reset Address
```

### domain:seed
Seed the given domain, or without an argument, seed all domains
```bash
php artisan domain:seed Address
```

### domain:publish-migration
Publish the migration files for the given domain, or without an argument publish all domains migrations.
```bash
php artisan domain:publish-migration Address
```

### domain:publish-config
Publish the given domain configuration files, or without an argument publish all domains configuration files.
```bash
php artisan domain:publish-config Address
```

### domain:publish-translation
Publish the translation files for the given domain, or without a specified domain publish all domains migrations.
```bash
php artisan domain:publish-translation Address
```

### domain:enable
Enable the given domain.
```bash
php artisan domain:enable Address
```

### domain:disable
Disable the given domain.
```bash
php artisan domain:disable Address
```
### domain:update
Update the given domain.
```bash
php artisan module:update Blog
```



## Credits

- [Nicolas Widart](https://github.com/salah3id)
- [David Carr](https://github.com/dcAddressdev)
- [gravitano](https://github.com/gravitano)
- [Anderson Andrade](https://github.com/andersao)
- [Salah Eid](https://github.com/salah3id)
- [All Contributors](../../contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.





