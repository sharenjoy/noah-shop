<?php

namespace Sharenjoy\NoahShop\Resources\ProductResource\Pages;

use Sharenjoy\NoahShop\Resources\ProductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
