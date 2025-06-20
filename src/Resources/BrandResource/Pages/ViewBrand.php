<?php

namespace Sharenjoy\NoahShop\Resources\BrandResource\Pages;

use Sharenjoy\NoahShop\Resources\BrandResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewBrand extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
