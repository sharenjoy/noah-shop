<?php

namespace Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateObjective extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = ObjectiveResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }

    protected function afterCreate(): void
    {
        $product = $this->record;

        // ResolveProductSpecsDataToRecords::run($product->specs, $product, 'create');
    }
}
