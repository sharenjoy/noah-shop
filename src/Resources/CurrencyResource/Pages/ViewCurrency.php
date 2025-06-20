<?php

namespace Sharenjoy\NoahShop\Resources\CurrencyResource\Pages;

use Sharenjoy\NoahShop\Resources\CurrencyResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewCurrency extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
