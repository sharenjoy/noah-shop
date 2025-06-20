<?php

namespace Sharenjoy\NoahShop\Resources\ProductSpecificationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Sharenjoy\NoahShop\Resources\ProductSpecificationResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateProductSpecification extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = ProductSpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
