<?php

namespace Sharenjoy\NoahShop\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sharenjoy\NoahShop\NoahShop
 */
class NoahShop extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sharenjoy\NoahShop\NoahShop::class;
    }
}
