<?php

namespace Sharenjoy\NoahShop\Resources\CurrencyResource\Pages;

use Sharenjoy\NoahShop\Resources\CurrencyResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
