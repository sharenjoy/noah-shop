<?php

namespace Sharenjoy\NoahShop\Resources\GiftproductResource\Pages;

use Sharenjoy\NoahShop\Resources\GiftproductResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;
use Filament\Resources\Pages\EditRecord;

class EditGiftproduct extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = GiftproductResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
