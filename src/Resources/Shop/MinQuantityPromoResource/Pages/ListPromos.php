<?php

namespace Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListPromos extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = MinQuantityPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
