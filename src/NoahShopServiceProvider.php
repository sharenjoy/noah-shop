<?php

namespace Sharenjoy\NoahShop;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sharenjoy\NoahShop\Commands\NoahShopCommand;

class NoahShopServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('noah-shop')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_noah_shop_table')
            ->hasCommand(NoahShopCommand::class);
    }
}
