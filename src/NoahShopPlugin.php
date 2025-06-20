<?php

namespace Sharenjoy\NoahShop;

use Filament\Contracts\Plugin;
use Filament\Panel;

class NoahShopPlugin implements Plugin
{
    protected bool $hasEmailVerifiedAt = false;

    public static function make(): NoahShopPlugin
    {
        return new NoahShopPlugin();
    }

    public function getId(): string
    {
        return 'noah-shop';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(config('noah-shop.plugins.resources'))
            ->pages(config('noah-shop.plugins.pages'))
            ->widgets(config('noah-shop.plugins.widgets'));
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
