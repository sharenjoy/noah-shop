<?php

namespace Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewPromo extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = CouponPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
