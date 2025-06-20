<?php

namespace Sharenjoy\NoahShop\Resources\GiftproductResource\Pages;

use Sharenjoy\NoahShop\Resources\GiftproductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;
use Filament\Resources\Pages\ListRecords;

class ListGiftproducts extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = GiftproductResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
