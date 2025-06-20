<?php

namespace Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditPromo extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = CouponPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
