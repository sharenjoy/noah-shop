<?php

namespace Sharenjoy\NoahShop\Resources\CurrencyResource\Pages;

use Sharenjoy\NoahShop\Resources\CurrencyResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
