<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\New;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Sharenjoy\NoahShop\Resources\Shop\NewOrderResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListOrders extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = NewOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery()->new();
    }
}
