<?php

namespace Sharenjoy\NoahShop\Resources\GiftproductResource\Pages;

use Sharenjoy\NoahShop\Resources\GiftproductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahViewRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewGiftproduct extends ViewRecord
{
    use NoahViewRecord;

    protected static string $resource = GiftproductResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
