<?php

namespace Sharenjoy\NoahShop\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Sharenjoy\NoahShop\Actions\ResolveProductSpecsDataToRecords;
use Sharenjoy\NoahShop\Resources\ProductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateProduct extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }

    protected function afterCreate(): void
    {
        $product = $this->record;

        ResolveProductSpecsDataToRecords::run($product->specs, $product, 'create');
    }
}
