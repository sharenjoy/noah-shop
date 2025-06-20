<?php

namespace Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreatePromo extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = CouponPromoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
