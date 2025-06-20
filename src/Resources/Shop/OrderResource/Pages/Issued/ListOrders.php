<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Issued;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Resources\Shop\IssuedOrderResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListOrders extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = IssuedOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery()->issued();
    }
}
