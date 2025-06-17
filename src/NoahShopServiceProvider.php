<?php

namespace Sharenjoy\NoahShop;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Schedule;
use Sharenjoy\NoahShop\Commands\NoahShopCommand;
use Sharenjoy\NoahShop\Http\View\Composers\Survey\SurveyComposer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasRoute('web')
            ->hasConfigFile([
                'noah-shop',
            ])
            ->hasViews()
            ->hasTranslations()
            ->discoversMigrations()
            ->hasAssets()
            ->hasCommands([
                NoahShopCommand::class,
            ]);
    }

    public function bootingPackage()
    {
        app()->make(ViewFactory::class)->composer('noah-cms::standard', SurveyComposer::class);
    }

    public function packageBooted()
    {
        \Illuminate\Database\Eloquent\Model::unguard();

        Schedule::command('noah-shop:test-command')->dailyAt('00:30');
    }
}
