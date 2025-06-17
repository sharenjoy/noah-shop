<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahCreateRecord;

class CreateEntry extends CreateRecord
{
    use NoahCreateRecord;

    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
