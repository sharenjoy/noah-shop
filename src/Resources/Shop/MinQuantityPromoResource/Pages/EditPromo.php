<?php

namespace Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditPromo extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = MinQuantityPromoResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
