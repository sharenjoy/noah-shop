<?php

namespace Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListPromos extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = CouponPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
