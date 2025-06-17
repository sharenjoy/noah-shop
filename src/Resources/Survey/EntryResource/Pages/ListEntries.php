<?php

namespace Sharenjoy\NoahShop\Resources\Survey\EntryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Sharenjoy\NoahShop\Resources\Survey\EntryResource;
use Sharenjoy\NoahCms\Resources\Traits\NoahListRecords;

class ListEntries extends ListRecords
{
    use NoahListRecords;

    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge([], $this->recordHeaderActions());
    }
}
