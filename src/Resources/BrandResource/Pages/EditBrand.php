<?php

namespace Sharenjoy\NoahShop\Resources\BrandResource\Pages;

use Sharenjoy\NoahShop\Resources\BrandResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
