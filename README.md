# This is my package noah-shop

## Installation

You can install the package via composer:

```bash
composer require sharenjoy/noah-shop:dev-main
```

Replace this to user model

```php
<?php

namespace App\Models;

use Sharenjoy\NoahShop\Models\User as NoahShopUser;

class User extends NoahShopUser {}
```

Update auth.php in config folder

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', \Sharenjoy\NoahShop\Models\User::class),
    ],
],
```

You can publish migrations and run migrate and other database related:

```bash
php artisan vendor:publish --tag="noah-shop-migrations"
```

```bash
php artisan migrate
```

```bash
php artisan shield:generate --all
```

You can publish assets and run the migrations with:

```bash
php artisan filament:assets
```

You can publish the assets using

```bash
php artisan vendor:publish --tag="noah-shop-assets"
```

You need publish the config file with:

```bash
php artisan vendor:publish --tag="noah-shop-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="noah-shop-views"
```

Optionally, you can publish the translations using

```bash
php artisan vendor:publish --tag="noah-shop-translations" --force
```

Npm install and build

```bash
npm install
npm run build
```

## Usage

```php
$noahShop = new Sharenjoy\NoahShop();
echo $noahShop->echoPhrase('Hello, Sharenjoy!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Ronald](https://github.com/sharenjoy)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
