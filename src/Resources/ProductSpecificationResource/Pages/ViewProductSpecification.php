<?php

namespace Sharenjoy\NoahShop\Resources\ProductSpecificationResource\Pages;

use Sharenjoy\NoahShop\Resources\ProductSpecificationResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewProductSpecification extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = ProductSpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
