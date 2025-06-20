<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Shipped;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Resources\Shop\ShippedOrderResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListOrders extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = ShippedOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery()->shipped();
    }
}
