# Laravel Multi Application

A simple loader that permits to split the project in multiple parts or even create multiple application. All the basic functions of laravel will be automatically loaded: Commands, Resources/views, Resources/public, routes, Exceptions/Handler, Providers

## Requirements

PHP 5.6.4 or later.

## Composer

You can install it via [Composer](https://getcomposer.org/) by typing the following command:

```bash
composer require railken/laravel-manager
```

## Installation

- Add the folder psr-4 to your `composer.json` and run `composer update`.
```json
{
    "autoload": {
        "psr-4": {
            "": "src/"
        }
    },
}
```
- Add the service provider to the `providers` array in `config/app.php`

```php
Railken\Laravel\App\AppServiceProvider::class,
```

- Replace "HandlerException in app/Exceptions/Handler" (Only laravel 5.3 +)

```php
use Railken\Laravel\App\Exceptions\Handler as ExceptionHandler;
```

## Usage

- Create a new src package folder with `php artisan railken:make:src Example` at the root of your project.

### Return view

```php
return view('Example::index');
```

### Assets

```php
{{ asset('src/Example/assets/welcome/main.css' )}}
```

### Exception Handler (Only laravel 5.3 +)

```php

    public function report(Exception $exception)
    {
        # Report only if it's a custom report
        # echo "A custom report";
    }
    
    public function render($request, Exception $exception)
    {
    	
        # Return only if it's a custom render
        # The first handler with a return in render method will be used
        # return parent::render($request, $exception);
        
    }
```
