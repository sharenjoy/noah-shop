<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahEditRecord;

class EditEntry extends EditRecord
{
    use NoahEditRecord;

    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
