<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditOrder extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
