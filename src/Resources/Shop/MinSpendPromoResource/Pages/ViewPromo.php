<?php

namespace Sharenjoy\NoahShop\Resources\Shop\MinSpendPromoResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Sharenjoy\NoahShop\Resources\Shop\MinSpendPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;

class ViewPromo extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = MinSpendPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
