<?php

namespace Sharenjoy\NoahShop\Resources\BrandResource\Pages;

use Sharenjoy\NoahShop\Resources\BrandResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
