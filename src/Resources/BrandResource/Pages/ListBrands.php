<?php

namespace Sharenjoy\NoahShop\Resources\BrandResource\Pages;

use Sharenjoy\NoahShop\Resources\BrandResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
