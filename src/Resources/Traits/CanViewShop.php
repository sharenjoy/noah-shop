<?php

namespace Sharenjoy\NoahShop\Resources\Traits;

use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Actions\Shop\ShopFeatured;

trait CanViewShop
{
    public static function canAccess(): bool
    {
        if (! ShopFeatured::run('shop')) {
            return false;
        }

        return parent::canAccess();
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! ShopFeatured::run('shop')) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }
}
